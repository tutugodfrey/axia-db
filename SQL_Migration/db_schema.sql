--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'SQL_ASCII';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: axia_legacy; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE axia_legacy WITH TEMPLATE = template0 ENCODING = 'SQL_ASCII' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';


ALTER DATABASE axia_legacy OWNER TO postgres;

\connect axia_legacy

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'SQL_ASCII';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: pp_types; Type: TYPE; Schema: public; Owner: axia
--

CREATE TYPE pp_types AS ENUM (
    'percentage',
    'points',
    'percentage-grossprofit',
    'points-calculateonly'
);


ALTER TYPE public.pp_types OWNER TO axia;

--
-- Name: uw_verified_type; Type: TYPE; Schema: public; Owner: axia
--

CREATE TYPE uw_verified_type AS ENUM (
    'nrp',
    'yn',
    'match'
);


ALTER TYPE public.uw_verified_type OWNER TO axia;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: commission_pricing; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE commission_pricing (
    merchant_id character varying(50) NOT NULL,
    user_id integer NOT NULL,
    c_month real NOT NULL,
    c_year real NOT NULL,
    multiple real,
    r_rate_pct real,
    r_per_item_fee real,
    r_statement_fee real,
    m_rate_pct real,
    m_per_item_fee real,
    m_statement_fee real,
    m_avg_ticket real,
    m_monthly_volume real,
    m_bet_code character varying(35),
    ref_seq_number integer,
    bet_extra_pct real,
    res_seq_number integer,
    products_services_type character varying(10) DEFAULT 'BC'::character varying NOT NULL,
    num_items real,
    ref_p_value real,
    res_p_value real,
    r_risk_assessment real,
    ref_p_type pp_types,
    res_p_type pp_types,
    ref_p_pct integer,
    res_p_pct integer
);


ALTER TABLE public.commission_pricing OWNER TO axia;

--
-- Name: calc_est_gross_profit(commission_pricing); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION calc_est_gross_profit(cp commission_pricing) RETURNS real
    LANGUAGE plpgsql
    AS $$
BEGIN
RETURN ((cp.m_monthly_volume * (cp.m_rate_pct + COALESCE(cp.bet_extra_pct, 0) - cp.r_rate_pct - COALESCE(cp.r_risk_assessment, 0)) / 100) +
        ((cp.m_monthly_volume / (CASE WHEN cp.m_avg_ticket > 0 THEN cp.m_avg_ticket ELSE 1 END)) *
    (cp.m_per_item_fee - cp.r_per_item_fee)) +
        (cp.m_statement_fee - cp.r_statement_fee));
END;
$$;


ALTER FUNCTION public.calc_est_gross_profit(cp commission_pricing) OWNER TO axia;

--
-- Name: fGetMerchantDBA(character varying); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION "fGetMerchantDBA"(character varying) RETURNS character varying
    LANGUAGE sql
    AS $_$SELECT merchant_dba FROM "merchant" WHERE merchant_id = $1$_$;


ALTER FUNCTION public."fGetMerchantDBA"(character varying) OWNER TO axia;

--
-- Name: fGetTransactionDesc(integer, integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION "fGetTransactionDesc"(integer, integer, integer, integer, integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$ DECLARE 
     v_note ALIAS FOR $1; 
     v_change ALIAS FOR $2;
     v_ach ALIAS FOR $3;
     v_order ALIAS FOR $4; 
     v_prog ALIAS FOR $5; 
     v_getnote integer; 
     v_desc varchar; 
 BEGIN 
     
     v_desc := NULL; 

     IF v_change > 0 THEN 
         SELECT INTO v_getnote COALESCE(merchant_note_id, 0) FROM merchant_change WHERE change_id = v_change;
     ELSIF v_note > 0 THEN
         v_getnote := v_note;
     ELSIF v_ach > 0 THEN
         SELECT INTO v_desc reason FROM merchant_ach WHERE ach_seq_number = v_ach; 
         RETURN v_desc;
     ELSIF v_order > 0 THEN
         SELECT INTO v_desc invoice_number FROM orders WHERE order_id = v_order; 
         RETURN v_desc;
     ELSIF v_prog > 0 THEN
         SELECT INTO v_desc terminal_number FROM equipment_programming WHERE programming_id = v_prog; 
         RETURN v_desc;
     ELSE  
         RETURN v_desc;
     END IF;

     SELECT INTO v_desc note_title FROM merchant_note WHERE merchant_note_id = v_getnote;      
         
     RETURN v_desc; 
 END; $_$;


ALTER FUNCTION public."fGetTransactionDesc"(integer, integer, integer, integer, integer) OWNER TO axia;

--
-- Name: fGetUserLastTransaction(integer, character varying); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION "fGetUserLastTransaction"(integer, character varying) RETURNS character varying
    LANGUAGE sql
    AS $_$SELECT system_transaction_date || chr(32) || system_transaction_time
FROM "system_transaction" WHERE system_transaction_id = (SELECT MAX(system_transaction_id) FROM "system_transaction" WHERE transaction_type = $2 AND user_id = $1);$_$;


ALTER FUNCTION public."fGetUserLastTransaction"(integer, character varying) OWNER TO axia;

--
-- Name: fGetUserName(integer); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION "fGetUserName"(integer) RETURNS character varying
    LANGUAGE sql
    AS $_$SELECT user_first_name || chr(32) || user_last_name
FROM "user" 
WHERE user_id = $1;$_$;


ALTER FUNCTION public."fGetUserName"(integer) OWNER TO axia;

--
-- Name: pci_billing_history_function(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION pci_billing_history_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF tg_op = 'DELETE' THEN
     INSERT INTO pci_billing_history(id, pci_billing_type_id, saq_merchant_id, billing_date, date_change, operation)
     VALUES (old.id, old.pci_billing_type_id, old.saq_merchant_id, old.billing_date, now(), tg_op);
     RETURN old;
  END IF;
  IF tg_op = 'UPDATE' THEN
     INSERT INTO pci_billing_history(id, pci_billing_type_id, saq_merchant_id, billing_date, date_change, operation)
     VALUES (old.id, old.pci_billing_type_id, old.saq_merchant_id, old.billing_date, now(), tg_op);
     RETURN new;
  END IF;
  IF tg_op = 'INSERT' THEN
     INSERT INTO pci_billing_history(id, pci_billing_type_id, saq_merchant_id, billing_date, date_change, operation)
     VALUES (new.id, new.pci_billing_type_id, new.saq_merchant_id, new.billing_date, now(), tg_op);
     RETURN new;
  END IF;
END
$$;


ALTER FUNCTION public.pci_billing_history_function() OWNER TO axia;

--
-- Name: pci_compliance_status_log_function(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION pci_compliance_status_log_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF tg_op = 'DELETE' THEN
     INSERT INTO pci_compliance_status_log(pci_compliance_date_type_id, saq_merchant_id, date_complete, date_change, operation)
     VALUES (old.pci_compliance_date_type_id, old.saq_merchant_id, old.date_complete, now(), tg_op);
     RETURN old;
  END IF;
  IF tg_op = 'INSERT' THEN
     INSERT INTO pci_compliance_status_log(pci_compliance_date_type_id, saq_merchant_id, date_complete, date_change, operation)
     VALUES (new.pci_compliance_date_type_id, new.saq_merchant_id, new.date_complete, now(), tg_op);
     RETURN new;
  END IF;
END
$$;


ALTER FUNCTION public.pci_compliance_status_log_function() OWNER TO axia;

--
-- Name: plpgsql_call_handler(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION plpgsql_call_handler() RETURNS opaque
    LANGUAGE c
    AS '$libdir/plpgsql', 'plpgsql_call_handler';


ALTER FUNCTION public.plpgsql_call_handler() OWNER TO postgres;

--
-- Name: reset_merchant_clock(character varying); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION reset_merchant_clock(character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
                BEGIN
                                UPDATE saq_merchant SET email_sent = NULL, billing_date = NULL, next_billing_date = NULL WHERE merchant_id = $1;
                                UPDATE merchant_pci SET compliance_fee = NULL WHERE merchant_id = $1;
                                DELETE from saq_merchant_pci_email_sent WHERE saq_merchant_id = (SELECT id FROM saq_merchant WHERE merchant_id = $1);
                                RETURN 1;
                END;
                $_$;


ALTER FUNCTION public.reset_merchant_clock(character varying) OWNER TO axia;

--
-- Name: update_mid(character varying); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION update_mid(mid character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$   
    DECLARE   
         tableRecord record;   
    BEGIN   
         FOR tableRecord IN    
              select table_name from information_schema.columns where column_name = 'merchant_id'   
         LOOP   
              --DELETE FROM tableRecord.table_name WHERE merchant_id = mid; 
     
              RAISE NOTICE 'Deleted mid from', tableRecord.table_name;   
             
         END LOOP;   
         RETURN;   
    END;   
    $$;


ALTER FUNCTION public.update_mid(mid character varying) OWNER TO axia;

--
-- Name: update_pci_compliance_3rd(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION update_pci_compliance_3rd() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
smid integer;
pcdtidsaq integer;
pcdtidscan integer;
BEGIN
pcdtidsaq := 4;
pcdtidscan := 5;
smid := id from saq_merchant where merchant_id = old.merchant_id;
IF tg_op = 'UPDATE' THEN
  IF (new.saq_completed_date IS NOT NULL and old.saq_completed_date IS NULL) OR (new.saq_completed_date > old.saq_completed_date) THEN
    INSERT INTO pci_compliance VALUES (NEXTVAL('pci_compliance_id_seq'), pcdtidsaq, smid, new.saq_completed_date);

    RETURN new;
  END IF;
  IF (new.last_security_scan IS NOT NULL and old.last_security_scan IS NULL) OR (new.last_security_scan > old.last_security_scan) THEN
    INSERT INTO pci_compliance VALUES (NEXTVAL('pci_compliance_id_seq'), pcdtidscan, smid, new.last_security_scan);
    RETURN new;
  END IF;
  RETURN NULL;
END IF;
END
$$;


ALTER FUNCTION public.update_pci_compliance_3rd() OWNER TO axia;

--
-- Name: update_pci_compliance_ax(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION update_pci_compliance_ax() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
pcdtid integer;
BEGIN
pcdtid := 1;
IF tg_op = 'UPDATE' THEN
  IF new.acknowledgement_name IS NOT NULL THEN
    INSERT INTO pci_compliance VALUES (NEXTVAL('pci_compliance_id_seq'), pcdtid, old.saq_merchant_id, new.datecomplete);
    UPDATE saq_merchant set next_billing_date = (new.datecomplete + interval '1 year') where id = old.saq_merchant_id;
    RETURN new;
  END IF;
    RETURN NULL;
END IF;
END
$$;


ALTER FUNCTION public.update_pci_compliance_ax() OWNER TO axia;

--
-- Name: update_pci_compliance_cs_del(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION update_pci_compliance_cs_del() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
smid integer;
pcdtidsaq integer;
pcdtidscan integer;
BEGIN
pcdtidsaq := 2;
pcdtidscan := 3;
smid := id from saq_merchant where substr(merchant_id,5,12) = substr(old.merchant_id,5,12);
IF tg_op = 'DELETE' THEN
  IF old.pci_compliance = 'Yes' THEN
    DELETE FROM pci_compliance WHERE saq_merchant_id = smid AND pci_compliance_date_type_id = pcdtidscan AND date_complete = old.first_scan_date;
    DELETE FROM pci_compliance WHERE saq_merchant_id = smid AND pci_compliance_date_type_id = pcdtidsaq AND date_complete = old.first_questionnaire_date;
    RETURN old;
  END IF;
  RETURN NULL;
END IF;
END
$$;


ALTER FUNCTION public.update_pci_compliance_cs_del() OWNER TO axia;

--
-- Name: update_pci_compliance_cs_saq_ins(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION update_pci_compliance_cs_saq_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
smid integer;
pcdtidsaq integer;
pcdtidscan integer;
BEGIN
pcdtidsaq := 2;
smid := id from saq_merchant where substr(merchant_id,5,12) = substr(new.merchant_id,5,12);
IF tg_op = 'INSERT' THEN
  IF new.pci_compliance = 'Yes' and new.first_scan_date IS NOT NULL THEN
    INSERT INTO pci_compliance VALUES (NEXTVAL('pci_compliance_id_seq'), pcdtidsaq, smid, new.first_scan_date);
    RETURN new;
  END IF;
  RETURN NULL;
END IF;
END
$$;


ALTER FUNCTION public.update_pci_compliance_cs_saq_ins() OWNER TO axia;

--
-- Name: update_pci_compliance_cs_scan_ins(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION update_pci_compliance_cs_scan_ins() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
smid integer;
pcdtidsaq integer;
pcdtidscan integer;
BEGIN
pcdtidscan := 3;
smid := id from saq_merchant where substr(merchant_id,5,12) = substr(new.merchant_id,5,12);
IF tg_op = 'INSERT' THEN
  IF new.pci_compliance = 'Yes' and new.first_questionnaire_date IS NOT NULL THEN
    INSERT INTO pci_compliance VALUES (NEXTVAL('pci_compliance_id_seq'), pcdtidscan, smid, new.first_questionnaire_date);
    RETURN new;
  END IF;
  RETURN NULL;
END IF;
END
$$;


ALTER FUNCTION public.update_pci_compliance_cs_scan_ins() OWNER TO axia;

--
-- Name: update_pci_compliance_cs_up(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION update_pci_compliance_cs_up() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
smid integer;
pcdtidsaq integer;
pcdtidscan integer;
BEGIN
pcdtidsaq := 2;
pcdtidscan := 3;
smid := id from saq_merchant where substr(merchant_id,5,12) = substr(old.merchant_id,5,12);
IF tg_op = 'UPDATE' THEN
  IF ((new.first_questionnaire_date IS NOT NULL AND old.first_questionnaire_date IS NULL) AND new.pci_compliance = 'Yes') OR ((new.first_questionnaire_date > old.first_questionnaire_date) AND new.pci_compliance = 'Yes') THEN
    INSERT INTO pci_compliance VALUES (NEXTVAL('pci_compliance_id_seq'), pcdtidsaq, smid, new.first_questionnaire_date);
    RETURN new;
  END IF;
  IF ((new.first_scan_date IS NOT NULL and old.first_scan_date IS NULL) AND new.pci_compliance = 'Yes') OR ((new.first_scan_date > old.first_scan_date) AND new.pci_compliance = 'Yes') THEN
    INSERT INTO pci_compliance VALUES (NEXTVAL('pci_compliance_id_seq'), pcdtidscan, smid, new.first_scan_date);
    RETURN new;
  END IF;
  RETURN NULL;
END IF;
END
$$;


ALTER FUNCTION public.update_pci_compliance_cs_up() OWNER TO axia;

--
-- Name: ach; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE ach (
    merchant_id character varying(50) NOT NULL,
    ach_mid character varying(20),
    ach_expected_annual_sales real,
    ach_average_transaction real,
    ach_estimated_max_transaction real,
    ach_written_pre_auth real,
    ach_nonwritten_pre_auth real,
    ach_merchant_initiated_perc real,
    ach_consumer_initiated_perc real,
    ach_monthly_gateway_fee real,
    ach_monthly_minimum_fee real,
    ach_statement_fee real,
    ach_batch_upload_fee real,
    ach_reject_fee real,
    ach_add_bank_orig_id_fee real,
    ach_file_fee real,
    ach_eft_ccd_nw real,
    ach_eft_ccd_w real,
    ach_eft_ppd_nw real,
    ach_eft_ppd_w real,
    ach_application_fee real,
    ach_expedite_fee real,
    ach_tele_training_fee real,
    ach_mi_w_dsb_bank_name character varying(50),
    ach_mi_w_dsb_routing_number integer,
    ach_mi_w_dsb_account_number integer,
    ach_mi_w_fee_bank_name character varying(50),
    ach_mi_w_fee_routing_number integer,
    ach_mi_w_fee_account_number integer,
    ach_mi_w_rej_bank_name character varying(50),
    ach_mi_w_rej_routing_number integer,
    ach_mi_w_rej_account_number integer,
    ach_mi_nw_dsb_account_number integer,
    ach_mi_nw_dsb_routing_number integer,
    ach_mi_nw_dsb_bank_name character varying(50),
    ach_mi_nw_fee_bank_name character varying(50),
    ach_mi_nw_fee_routing_number integer,
    ach_mi_nw_fee_account_number integer,
    ach_mi_nw_rej_bank_name character varying(50),
    ach_mi_nw_rej_routing_number integer,
    ach_mi_nw_rej_account_number integer,
    ach_ci_w_dsb_bank_name character varying(50),
    ach_ci_w_dsb_routing_number integer,
    ach_ci_w_dsb_account_number integer,
    ach_ci_w_fee_bank_name character varying(50),
    ach_ci_w_fee_routing_number integer,
    ach_ci_w_fee_account_number integer,
    ach_ci_w_rej_bank_name character varying(50),
    ach_ci_w_rej_routing_number integer,
    ach_ci_w_rej_account_number integer,
    ach_ci_nw_dsb_bank_name character varying(50),
    ach_ci_nw_dsb_routing_number integer,
    ach_ci_nw_dsb_account_number integer,
    ach_ci_nw_fee_bank_name character varying(50),
    ach_ci_nw_fee_routing_number integer,
    ach_ci_nw_fee_account_number integer,
    ach_ci_nw_rej_bank_name character varying(50),
    ach_ci_nw_rej_routing_number integer,
    ach_ci_nw_rej_account_number integer,
    ach_rate real
);


ALTER TABLE public.ach OWNER TO axia;

--
-- Name: address; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE address (
    address_id integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    address_type character varying(10) NOT NULL,
    owner_id integer,
    address_title character varying(50),
    address_street character varying(100),
    address_city character varying(50),
    address_state character varying(2),
    address_zip character varying(20),
    address_phone character varying(20),
    address_fax character varying(20),
    address_phone2 character varying(20),
    address_phone_ext character varying(5),
    address_phone2_ext character varying(5)
);


ALTER TABLE public.address OWNER TO axia;

--
-- Name: address_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE address_type (
    address_type character varying(10) NOT NULL,
    address_type_description character varying(50)
);


ALTER TABLE public.address_type OWNER TO axia;

--
-- Name: adjustments; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE adjustments (
    adj_seq_number integer NOT NULL,
    user_id integer NOT NULL,
    adj_date date,
    adj_description character varying(100),
    adj_amount numeric(15,3)
);


ALTER TABLE public.adjustments OWNER TO axia;

--
-- Name: adjustments_t; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE adjustments_t (
    adj_seq_number integer NOT NULL,
    user_id integer NOT NULL,
    adj_date date,
    adj_description character varying(100),
    adj_amount real
);


ALTER TABLE public.adjustments_t OWNER TO axia;

--
-- Name: admin_entity_view; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE admin_entity_view (
    user_id integer NOT NULL,
    entity character varying(10) NOT NULL
);


ALTER TABLE public.admin_entity_view OWNER TO axia;

--
-- Name: amex; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE amex (
    merchant_id character varying(50) NOT NULL,
    amex_processing_rate real,
    amex_per_item_fee real
);


ALTER TABLE public.amex OWNER TO axia;

--
-- Name: authorize; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE authorize (
    merchant_id character varying(50) NOT NULL,
    mid character varying(20),
    transaction_fee real,
    monthly_fee real
);


ALTER TABLE public.authorize OWNER TO axia;

--
-- Name: saq_merchant_survey_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchant_survey_xref (
    id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    saq_survey_id integer NOT NULL,
    saq_eligibility_survey_id integer,
    saq_confirmation_survey_id integer,
    datestart timestamp without time zone NOT NULL,
    datecomplete timestamp without time zone,
    ip character varying(15),
    acknowledgement_name character varying(255),
    acknowledgement_title character varying(255),
    acknowledgement_company character varying(255),
    resolution text
);


ALTER TABLE public.saq_merchant_survey_xref OWNER TO axia;

--
-- Name: axia_compliant; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW axia_compliant AS
 SELECT smsx.saq_merchant_id,
    smsx.datecomplete
   FROM saq_merchant_survey_xref smsx
  WHERE (smsx.acknowledgement_name IS NOT NULL);


ALTER TABLE public.axia_compliant OWNER TO axia;

--
-- Name: bankcard; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE bankcard (
    merchant_id character varying(50) NOT NULL,
    bc_mid character varying(20),
    bet_code character varying(50),
    bc_processing_rate real,
    bc_per_item_fee real,
    bc_monthly_volume real,
    bc_average_ticket real,
    bc_max_transaction_amount real,
    bc_card_present_swipe real,
    bc_card_not_present real,
    bc_card_present_imprint real,
    bc_direct_to_consumer real,
    bc_business_to_business real,
    bc_government real,
    bc_annual_fee real,
    bc_statement_fee real,
    bc_min_month_process_fee real,
    bc_chargeback_fee real,
    bc_aru_fee real,
    bc_voice_auth_fee real,
    bc_te_amex_number character varying(255),
    bc_te_amex_auth_fee real,
    bc_te_diners_club_number character varying(255),
    bc_te_diners_club_auth_fee real,
    bc_te_discover_number character varying(255),
    bc_te_discover_auth_fee real,
    bc_te_jcb_number character varying(255),
    bc_te_jcb_auth_fee real,
    bc_pt_monthly_support_fee real,
    bc_pt_online_mer_report_fee real,
    bc_pt_mobile_access_fee real,
    bc_pt_mobile_transaction_fee real,
    bc_pt_application_fee real,
    bc_pt_expedite_fee real,
    bc_pt_mobile_setup_fee real,
    bc_pt_equip_fee real,
    bc_pt_phys_prod_tele_train_fee real,
    bc_pt_equip_reprog_fee real,
    bc_vt_monthly_support_fee real,
    bc_vt_gateway_access_fee real,
    bc_vt_application_fee real,
    bc_vt_expedite_fee real,
    bc_vt_prod_tele_train_fee real,
    bc_vt_lease_rental_deposit real,
    bc_hidden_per_item_fee real,
    bc_te_amex_number_disp character varying(4),
    bc_te_diners_club_number_disp character varying(4),
    bc_te_discover_number_disp character varying(4),
    bc_te_jcb_number_disp character varying(4),
    bc_hidden_per_item_fee_cross real,
    bc_eft_secure_flag integer,
    bc_pos_partner_flag integer,
    bc_pt_online_rep_report_fee real,
    bc_micros_ip_flag integer,
    bc_micros_dialup_flag integer,
    bc_micros_per_item_fee real,
    bc_te_num_items real,
    bc_wireless_flag integer,
    bc_wireless_terminals integer,
    bc_usaepay_flag integer,
    bc_epay_retail_flag integer,
    bc_usaepay_rep_gtwy_cost_id integer,
    bc_usaepay_rep_gtwy_add_cost_id integer,
    bc_card_not_present_internet real,
    bc_tgate_flag integer,
    bc_petro_flag integer,
    bc_risk_assessment real,
    bc_gw_gateway_id integer,
    bc_gw_rep_rate real,
    bc_gw_rep_per_item real,
    bc_gw_rep_statement real,
    bc_gw_rep_features text
);


ALTER TABLE public.bankcard OWNER TO axia;

--
-- Name: bet; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE bet (
    bet_code character varying(50) NOT NULL,
    bet_processing_rate real,
    bet_processing_rate_2 real,
    bet_processing_rate_3 real,
    bet_per_item_fee real,
    bet_per_item_fee_2 real,
    bet_per_item_fee_3 real,
    bet_business_rate real,
    bet_business_rate_2 real,
    bet_per_item_fee_bus real,
    bet_per_item_fee_bus_2 real,
    bet_extra_pct real
);


ALTER TABLE public.bet OWNER TO axia;

--
-- Name: cancellation_fee; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE cancellation_fee (
    cancellation_fee_id integer NOT NULL,
    cancellation_fee_description character varying(50) NOT NULL
);


ALTER TABLE public.cancellation_fee OWNER TO axia;

--
-- Name: card_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE card_type (
    card_type character varying(10) NOT NULL,
    card_type_description character varying(20)
);


ALTER TABLE public.card_type OWNER TO axia;

--
-- Name: change_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE change_type (
    change_type integer NOT NULL,
    change_type_description character varying(50)
);


ALTER TABLE public.change_type OWNER TO axia;

--
-- Name: check_guarantee; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE check_guarantee (
    merchant_id character varying(50) NOT NULL,
    cg_mid character varying(20),
    cg_station_number character varying(20),
    cg_account_number character varying(20),
    cg_transaction_rate real,
    cg_per_item_fee real,
    cg_monthly_fee real,
    cg_monthly_minimum_fee real,
    cg_application_fee real
);


ALTER TABLE public.check_guarantee OWNER TO axia;

--
-- Name: commission_report; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE commission_report (
    c_month integer,
    c_year integer,
    c_retail real,
    c_rep_cost real,
    c_shipping real,
    c_app_rtl real,
    c_app_cost real,
    c_install real,
    c_expecting real,
    c_from_nw1 real,
    c_other_cost real,
    c_from_other real,
    c_business real,
    user_id integer,
    status character varying(5),
    order_id integer,
    axia_invoice_number bigint,
    ach_seq_number integer,
    description character varying(200),
    merchant_id character varying(50),
    split_commissions boolean DEFAULT false NOT NULL,
    ref_seq_number integer,
    res_seq_number integer,
    partner_id integer,
    partner_exclude_volume boolean
);


ALTER TABLE public.commission_report OWNER TO axia;

--
-- Name: commission_report_old; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE commission_report_old (
    merchant_id_old character varying(50) NOT NULL,
    c_month integer,
    c_year integer,
    c_retail real,
    c_rep_cost real,
    c_shipping real,
    c_app_rtl real,
    c_app_cost real,
    c_install real,
    c_expecting real,
    c_from_nw1 real,
    c_other_cost real,
    c_from_other real,
    c_business real,
    user_id integer,
    status character varying(5),
    order_id integer,
    axia_invoice_number bigint,
    ach_seq_number integer,
    description character varying(200),
    merchant_id character varying(50)
);


ALTER TABLE public.commission_report_old OWNER TO axia;

--
-- Name: merchant; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant (
    merchant_id character varying(50) NOT NULL,
    user_id integer NOT NULL,
    merchant_mid character varying(20) NOT NULL,
    merchant_name character varying(100),
    merchant_dba character varying(100),
    merchant_contact character varying(50),
    merchant_email character varying(50),
    merchant_ownership_type character varying(30),
    merchant_fed_tax_id character varying(255),
    merchant_d_and_b character varying(255),
    inactive_date date,
    active_date date,
    ref_seq_number integer,
    network_id integer,
    merchant_buslevel character varying(50),
    merchant_sic integer,
    entity character varying(10),
    group_id character varying(10),
    merchant_bustype character varying(100),
    merchant_url character varying(100),
    merchant_fed_tax_id_disp character varying(4),
    merchant_d_and_b_disp character varying(4),
    active integer,
    cancellation_fee_id integer,
    merchant_contact_position character varying(50),
    merchant_mail_contact character varying(50),
    res_seq_number integer,
    bin_id integer,
    acquirer_id integer,
    reporting_user character varying(65),
    merchant_ps_sold character varying(100),
    ref_p_type pp_types,
    ref_p_value real,
    res_p_type pp_types,
    res_p_value real,
    ref_p_pct integer,
    res_p_pct integer,
    onlineapp_application_id integer,
    partner_id integer,
    partner_exclude_volume boolean,
    aggregated boolean,
    cobranded_application_id integer
);


ALTER TABLE public.merchant OWNER TO axia;

--
-- Name: merchant_cancellation; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_cancellation (
    merchant_id character varying(50) NOT NULL,
    date_submitted date,
    date_completed date,
    fee_charged real,
    reason text,
    status character varying(5) DEFAULT 'PEND'::character varying NOT NULL,
    axia_invoice_number bigint,
    date_inactive date,
    merchant_cancellation_subreason character varying(255),
    subreason_id integer
);


ALTER TABLE public.merchant_cancellation OWNER TO axia;

--
-- Name: merchant_pci; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_pci (
    merchant_id character varying(50) NOT NULL,
    compliance_level integer DEFAULT 4 NOT NULL,
    saq_completed_date date,
    compliance_fee double precision,
    insurance_fee double precision,
    last_security_scan date,
    scanning_company character varying(255)
);


ALTER TABLE public.merchant_pci OWNER TO axia;

--
-- Name: saq_control_scan; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_control_scan (
    merchant_id character varying NOT NULL,
    saq_type character varying(4),
    first_scan_date date,
    first_questionnaire_date date,
    scan_status character varying,
    questionnaire_status character varying,
    creation_date date,
    dba character varying(80),
    quarterly_scan_fee double precision DEFAULT (11.949999999999999)::double precision NOT NULL,
    sua timestamp with time zone,
    pci_compliance character varying(3),
    offline_compliance character varying(3),
    compliant_date date,
    host_count integer,
    submids text
);


ALTER TABLE public.saq_control_scan OWNER TO axia;

--
-- Name: saq_merchant; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchant (
    id integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    merchant_name character varying(255) NOT NULL,
    merchant_email character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    email_sent timestamp without time zone,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone
);


ALTER TABLE public.saq_merchant OWNER TO axia;

--
-- Name: timeline_entries; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE timeline_entries (
    merchant_id character varying(50) NOT NULL,
    timeline_item character varying(50) NOT NULL,
    timeline_date_completed date,
    action_flag boolean DEFAULT false
);


ALTER TABLE public.timeline_entries OWNER TO axia;

--
-- Name: saq_eligible; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW saq_eligible AS
 SELECT sm.id,
    sm.merchant_id,
    sm.merchant_email,
    sm.merchant_name,
    sm.password,
    sm.email_sent,
    scs.saq_type,
    scs.first_scan_date,
    scs.first_questionnaire_date,
    scs.scan_status,
    scs.questionnaire_status,
    scs.pci_compliance,
    scs.creation_date,
    scs.sua,
    mp.saq_completed_date,
    sm.billing_date,
    sm.next_billing_date,
    scs.quarterly_scan_fee,
    mp.compliance_fee,
    mp.insurance_fee
   FROM (((((((saq_merchant sm
     LEFT JOIN merchant m ON ((((sm.merchant_id)::text = (m.merchant_id)::text) AND (m.active = 1))))
     LEFT JOIN merchant_pci mp ON (((sm.merchant_id)::text = (mp.merchant_id)::text)))
     LEFT JOIN saq_control_scan scs ON ((substr((sm.merchant_id)::text, 5, 12) = substr((scs.merchant_id)::text, 5, 12))))
     LEFT JOIN merchant_cancellation mc ON ((((sm.merchant_id)::text = (mc.merchant_id)::text) AND (mc.date_completed IS NOT NULL))))
     LEFT JOIN timeline_entries tes ON ((((sm.merchant_id)::text = (tes.merchant_id)::text) AND (((tes.timeline_item)::text = 'SUB'::text) AND (tes.timeline_date_completed < '2010-08-01'::date)))))
     LEFT JOIN timeline_entries tei ON ((((sm.merchant_id)::text = (tei.merchant_id)::text) AND ((tei.timeline_item)::text = 'INS'::text))))
     LEFT JOIN bankcard b ON (((sm.merchant_id)::text = (b.merchant_id)::text)))
  WHERE ((((((sm.merchant_email)::text ~~ '%@%'::text) AND (m.merchant_id IS NOT NULL)) AND ((tei.merchant_id IS NOT NULL) OR (tes.merchant_id IS NOT NULL))) AND (b.merchant_id IS NOT NULL)) AND (mc.merchant_id IS NULL));


ALTER TABLE public.saq_eligible OWNER TO axia;

--
-- Name: saq_survey; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_survey (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    saq_level character varying(4),
    eligibility_survey_id integer,
    confirmation_survey_id integer
);


ALTER TABLE public.saq_survey OWNER TO axia;

--
-- Name: compliant_merchants; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW compliant_merchants AS
 SELECT se.id,
    se.merchant_id,
    se.merchant_email,
    se.merchant_name,
    se.password,
    se.email_sent,
    se.saq_type,
    se.first_scan_date,
    se.first_questionnaire_date,
    se.scan_status,
    se.questionnaire_status,
    se.pci_compliance,
    se.creation_date,
    se.sua,
    se.saq_completed_date,
    se.billing_date,
    se.next_billing_date,
    se.quarterly_scan_fee,
    se.compliance_fee,
    se.insurance_fee,
    smsx.datecomplete,
    ss.saq_level
   FROM ((saq_eligible se
     LEFT JOIN saq_merchant_survey_xref smsx ON (((se.id = smsx.saq_merchant_id) AND (smsx.acknowledgement_name IS NOT NULL))))
     LEFT JOIN saq_survey ss ON ((ss.id = smsx.saq_survey_id)))
  WHERE ((((se.pci_compliance)::text = 'Yes'::text) OR ((se.saq_completed_date IS NOT NULL) AND (se.saq_completed_date > (now() - '1 year'::interval)))) OR ((smsx.datecomplete IS NOT NULL) AND (smsx.datecomplete > (now() - '1 year'::interval))));


ALTER TABLE public.compliant_merchants OWNER TO axia;

--
-- Name: compliant_merchants_cs; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW compliant_merchants_cs AS
 SELECT se.id,
    se.merchant_id,
    se.merchant_email,
    se.merchant_name,
    se.password,
    se.email_sent,
    se.saq_type,
    se.first_scan_date,
    se.first_questionnaire_date,
    se.scan_status,
    se.questionnaire_status,
    se.pci_compliance,
    se.creation_date,
    se.sua,
    se.saq_completed_date,
    se.billing_date,
    se.next_billing_date,
    se.quarterly_scan_fee,
    se.compliance_fee,
    se.insurance_fee
   FROM saq_eligible se
  WHERE ((se.pci_compliance)::text = 'Yes'::text);


ALTER TABLE public.compliant_merchants_cs OWNER TO axia;

--
-- Name: compliant_merchants_mv; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE compliant_merchants_mv (
    id integer,
    merchant_id character varying(50),
    merchant_email character varying(100),
    merchant_name character varying(255),
    password character varying(255),
    email_sent timestamp without time zone,
    saq_type character varying(4),
    first_scan_date date,
    first_questionnaire_date date,
    scan_status character varying,
    questionnaire_status character varying,
    pci_compliance character varying(3),
    creation_date date,
    sua timestamp with time zone,
    saq_completed_date date,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone,
    quarterly_scan_fee double precision,
    compliance_fee double precision,
    insurance_fee double precision,
    datecomplete timestamp without time zone,
    saq_level character varying(4)
);


ALTER TABLE public.compliant_merchants_mv OWNER TO axia;

--
-- Name: debit; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE debit (
    merchant_id character varying(50) NOT NULL,
    mid character varying(20),
    transaction_fee real,
    monthly_fee real,
    monthly_volume real,
    monthly_num_items real,
    rate_pct real,
    acquirer_id integer
);


ALTER TABLE public.debit OWNER TO axia;

--
-- Name: debit_acquirer; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE debit_acquirer (
    debit_acquirer_id integer NOT NULL,
    debit_acquirer character varying(137) NOT NULL
);


ALTER TABLE public.debit_acquirer OWNER TO axia;

--
-- Name: discover; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE discover (
    merchant_id character varying(50) NOT NULL,
    bet_code character varying(50),
    disc_processing_rate real,
    disc_per_item_fee real,
    disc_statement_fee real,
    disc_monthly_volume real,
    disc_average_ticket real,
    disc_risk_assessment real,
    disc_gw_gateway_id integer,
    disc_gw_rep_rate real,
    disc_gw_rep_per_item real,
    disc_gw_rep_features text
);


ALTER TABLE public.discover OWNER TO axia;

--
-- Name: discover_bet; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE discover_bet (
    bet_code character varying(50) NOT NULL,
    bet_extra_pct real
);


ALTER TABLE public.discover_bet OWNER TO axia;

--
-- Name: discover_user_bet_table; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE discover_user_bet_table (
    bet_code character varying(50) NOT NULL,
    user_id integer NOT NULL,
    network character varying(20) NOT NULL,
    rate real,
    pi real
);


ALTER TABLE public.discover_user_bet_table OWNER TO axia;

--
-- Name: ebt; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE ebt (
    merchant_id character varying(50) NOT NULL,
    mid character varying(20),
    transaction_fee real,
    monthly_fee real
);


ALTER TABLE public.ebt OWNER TO axia;

--
-- Name: entity; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE entity (
    entity character varying(10) NOT NULL,
    entity_name character varying(50)
);


ALTER TABLE public.entity OWNER TO axia;

--
-- Name: equipment_item; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_item (
    equipment_item integer NOT NULL,
    equipment_type character varying(10) NOT NULL,
    equipment_item_description character varying(100),
    equipment_item_true_price real,
    equipment_item_rep_price real,
    active integer,
    warranty integer
);


ALTER TABLE public.equipment_item OWNER TO axia;

--
-- Name: equipment_programming; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_programming (
    programming_id integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    terminal_number character varying(20),
    hardware_serial character varying(20),
    terminal_type character varying(20),
    network character varying(20),
    provider character varying(20),
    app_id character varying(20),
    status character varying(5),
    date_entered date,
    date_changed date,
    user_id integer,
    serial_number character varying(20),
    pin_pad character varying(20),
    printer character varying(20),
    auto_close character varying(20),
    chain character varying(6),
    agent character varying(6),
    gateway_id integer,
    version character varying(20)
);


ALTER TABLE public.equipment_programming OWNER TO axia;

--
-- Name: equipment_programming_type_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_programming_type_xref (
    programming_id integer NOT NULL,
    programming_type character varying(10) NOT NULL
);


ALTER TABLE public.equipment_programming_type_xref OWNER TO axia;

--
-- Name: equipment_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_type (
    equipment_type character varying(10) NOT NULL,
    equipment_type_description character varying(50)
);


ALTER TABLE public.equipment_type OWNER TO axia;

--
-- Name: merchants_to_bill; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchants_to_bill (
    merchant_id text,
    billing_date timestamp without time zone
);


ALTER TABLE public.merchants_to_bill OWNER TO axia;

--
-- Name: tsys_non_compliances; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tsys_non_compliances (
    merchant_id character varying(20),
    dba character varying(50),
    assoc_id integer,
    billing_method character varying(10),
    description character varying(50),
    type character varying(50),
    unit integer,
    amount character varying(10),
    search_type character varying(20)
);


ALTER TABLE public.tsys_non_compliances OWNER TO axia;

--
-- Name: foo; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW foo AS
 SELECT tnc.dba,
    mtb.merchant_id,
    mtb.billing_date
   FROM (merchants_to_bill mtb
     LEFT JOIN tsys_non_compliances tnc ON ((mtb.merchant_id = (tnc.merchant_id)::text)));


ALTER TABLE public.foo OWNER TO axia;

--
-- Name: gateway; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gateway (
    merchant_id character varying(50) NOT NULL,
    gw_rate real,
    gw_per_item real,
    gw_statement real,
    gw_epay_retail_num integer,
    gw_usaepay_rep_gtwy_cost_id integer,
    gw_usaepay_rep_gtwy_add_cost_id integer
);


ALTER TABLE public.gateway OWNER TO axia;

--
-- Name: gateway1; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gateway1 (
    merchant_id character varying(50) NOT NULL,
    gw1_rate real,
    gw1_per_item real,
    gw1_statement real,
    gw1_rep_rate real,
    gw1_rep_per_item real,
    gw1_rep_statement real,
    gw1_rep_features text,
    gw1_gateway_id integer NOT NULL,
    gw1_monthly_volume real,
    gw1_monthly_num_items real
);


ALTER TABLE public.gateway1 OWNER TO axia;

--
-- Name: gateway2; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gateway2 (
    merchant_id character varying(50) NOT NULL,
    gw2_rate real,
    gw2_per_item real,
    gw2_statement real,
    gw2_rep_rate real,
    gw2_rep_per_item real,
    gw2_rep_statement real,
    gw2_rep_features text,
    gw2_gateway_id integer NOT NULL,
    gw2_monthly_volume real,
    gw2_monthly_num_items real
);


ALTER TABLE public.gateway2 OWNER TO axia;

--
-- Name: gateways; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gateways (
    id integer NOT NULL,
    name character varying(57) NOT NULL
);


ALTER TABLE public.gateways OWNER TO axia;

--
-- Name: gift_card; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gift_card (
    merchant_id character varying(50) NOT NULL,
    gc_mid character varying(20),
    gc_magstripe_loyalty_item_fee real,
    gc_magstripe_gift_item_fee real,
    gc_chip_card_one_rate real,
    gc_chip_card_gift_item_fee real,
    gc_chip_card_loyalty_item_fee real,
    gc_smart_card_printing real,
    gc_mag_card_printing real,
    gc_loyalty_mgmt_database real,
    gc_statement_fee real,
    gc_application_fee real,
    gc_equipment_fee real,
    gc_misc_supplies real,
    gc_merch_prov_art_setup_fee real,
    gc_news_provider_artwork_fee real,
    gc_training_fee real,
    gc_plan character varying(8),
    CONSTRAINT gift_card_gc_plan CHECK ((((gc_plan)::text = ('one_rate'::character varying)::text) OR ((gc_plan)::text = ('per_item'::character varying)::text)))
);


ALTER TABLE public.gift_card OWNER TO axia;

--
-- Name: group; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE "group" (
    group_id character varying(10) NOT NULL,
    group_description character varying(50),
    active boolean DEFAULT true NOT NULL
);


ALTER TABLE public."group" OWNER TO axia;

--
-- Name: last_deposit_report; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE last_deposit_report (
    merchant_id character varying(50),
    merchant_dba character varying(100),
    last_deposit_date date,
    user_id integer,
    monthly_volume numeric(10,2)
);


ALTER TABLE public.last_deposit_report OWNER TO axia;

--
-- Name: merchant_ach; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_ach (
    ach_seq_number integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    date_submitted date,
    date_completed date,
    reason character varying(100),
    credit_amount double precision,
    debit_amount double precision,
    reason_other character varying(100),
    status character varying(5),
    user_id integer,
    invoice_number bigint,
    app_status_id integer,
    billing_option_id integer,
    commission_month character varying(8),
    tax real
);


ALTER TABLE public.merchant_ach OWNER TO axia;

--
-- Name: merchant_ach_app_status; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_ach_app_status (
    app_status_id integer NOT NULL,
    app_status_description character varying(50) NOT NULL,
    rank integer,
    app_true_price real,
    app_rep_price real
);


ALTER TABLE public.merchant_ach_app_status OWNER TO axia;

--
-- Name: merchant_ach_billing_option; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_ach_billing_option (
    billing_option_id integer NOT NULL,
    billing_option_description character varying(50) NOT NULL
);


ALTER TABLE public.merchant_ach_billing_option OWNER TO axia;

--
-- Name: merchant_acquirer; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_acquirer (
    acquirer_id integer NOT NULL,
    acquirer character varying(21) NOT NULL
);


ALTER TABLE public.merchant_acquirer OWNER TO axia;

--
-- Name: merchant_bank; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_bank (
    merchant_id character varying(50) NOT NULL,
    bank_routing_number character varying(255),
    bank_dda_number character varying(255),
    bank_name character varying(60),
    bank_routing_number_disp character varying(4),
    bank_dda_number_disp character varying(4),
    fees_routing_number character varying(255),
    fees_dda_number character varying(255),
    fees_routing_number_disp character varying(4),
    fees_dda_number_disp character varying(4)
);


ALTER TABLE public.merchant_bank OWNER TO axia;

--
-- Name: merchant_bin; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_bin (
    bin_id integer NOT NULL,
    bin character varying(21) NOT NULL
);


ALTER TABLE public.merchant_bin OWNER TO axia;

--
-- Name: merchant_cancellation_subreason; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_cancellation_subreason (
    id integer NOT NULL,
    name character varying(65) NOT NULL,
    visible boolean DEFAULT true NOT NULL
);


ALTER TABLE public.merchant_cancellation_subreason OWNER TO axia;

--
-- Name: merchant_card_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_card_types (
    merchant_id character varying(50) NOT NULL,
    card_type character varying(10) NOT NULL
);


ALTER TABLE public.merchant_card_types OWNER TO axia;

--
-- Name: merchant_change; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_change (
    change_id integer NOT NULL,
    change_type integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    user_id integer NOT NULL,
    status character varying(5) NOT NULL,
    date_entered date NOT NULL,
    time_entered time without time zone NOT NULL,
    date_approved date,
    time_approved time without time zone,
    change_data text,
    merchant_note_id integer,
    approved_by integer,
    "programming_id " integer
);


ALTER TABLE public.merchant_change OWNER TO axia;

--
-- Name: merchant_note; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_note (
    merchant_note_id integer NOT NULL,
    note_type character varying(10) NOT NULL,
    user_id integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    note_date date,
    note text,
    note_title character varying(200),
    general_status character varying(5),
    date_changed date,
    critical integer,
    note_sent integer
);


ALTER TABLE public.merchant_note OWNER TO axia;

--
-- Name: merchant_owner; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_owner (
    owner_id integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    owner_social_sec_no character varying(255),
    owner_equity integer,
    owner_name character varying(100),
    owner_title character varying(40),
    owner_social_sec_no_disp character varying(4)
);


ALTER TABLE public.merchant_owner OWNER TO axia;

--
-- Name: merchant_reference; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reference (
    merchant_ref_seq_number integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    merchant_ref_type character varying(10) NOT NULL,
    bank_name character varying(100),
    person_name character varying(100),
    phone character varying(20)
);


ALTER TABLE public.merchant_reference OWNER TO axia;

--
-- Name: merchant_reference_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reference_type (
    merchant_ref_type character varying(10) NOT NULL,
    merchant_ref_type_desc character varying(50) NOT NULL
);


ALTER TABLE public.merchant_reference_type OWNER TO axia;

--
-- Name: merchant_reject; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject (
    id integer NOT NULL,
    merchant_id character varying(255) NOT NULL,
    trace bigint NOT NULL,
    reject_date date NOT NULL,
    typeid integer NOT NULL,
    code character varying(9) NOT NULL,
    amount numeric(8,2) DEFAULT 0 NOT NULL,
    recurranceid integer NOT NULL,
    open boolean DEFAULT true NOT NULL,
    loss_axia real,
    loss_mgr1 real,
    loss_mgr2 real,
    loss_rep real
);


ALTER TABLE public.merchant_reject OWNER TO axia;

--
-- Name: merchant_reject_line; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_line (
    id integer NOT NULL,
    rejectid integer NOT NULL,
    fee real,
    statusid integer NOT NULL,
    status_date date,
    notes text
);


ALTER TABLE public.merchant_reject_line OWNER TO axia;

--
-- Name: merchant_reject_recurrance; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_recurrance (
    id integer NOT NULL,
    name character varying(57) NOT NULL
);


ALTER TABLE public.merchant_reject_recurrance OWNER TO axia;

--
-- Name: merchant_reject_status; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_status (
    id integer NOT NULL,
    name character varying(57) NOT NULL,
    collected boolean,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.merchant_reject_status OWNER TO axia;

--
-- Name: merchant_reject_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_type (
    id integer NOT NULL,
    name character varying(57) NOT NULL
);


ALTER TABLE public.merchant_reject_type OWNER TO axia;

--
-- Name: merchant_uw; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_uw (
    merchant_id character varying(255) NOT NULL,
    tier_assignment integer,
    credit_pct real,
    chargeback_pct real,
    final_status_id integer,
    final_approved_id integer,
    final_date date,
    final_notes text,
    mcc character varying(255),
    expedited boolean DEFAULT false NOT NULL
);


ALTER TABLE public.merchant_uw OWNER TO axia;

--
-- Name: merchant_uw_final_approved; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_uw_final_approved (
    id integer NOT NULL,
    name character varying(21) NOT NULL
);


ALTER TABLE public.merchant_uw_final_approved OWNER TO axia;

--
-- Name: merchant_uw_final_status; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_uw_final_status (
    id integer NOT NULL,
    name character varying(21) NOT NULL
);


ALTER TABLE public.merchant_uw_final_status OWNER TO axia;

--
-- Name: network; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE network (
    network_id integer NOT NULL,
    network_description character varying(50)
);


ALTER TABLE public.network OWNER TO axia;

--
-- Name: non_compliant_merchants; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW non_compliant_merchants AS
 SELECT se.id,
    se.merchant_id,
    se.merchant_email,
    se.merchant_name,
    se.password,
    se.email_sent,
    se.saq_type,
    se.first_scan_date,
    se.first_questionnaire_date,
    se.scan_status,
    se.questionnaire_status,
    se.pci_compliance,
    se.creation_date,
    se.sua,
    se.saq_completed_date,
    se.billing_date,
    se.next_billing_date,
    se.quarterly_scan_fee,
    se.compliance_fee,
    se.insurance_fee
   FROM (saq_eligible se
     LEFT JOIN compliant_merchants cm ON (((se.merchant_id)::text = (cm.merchant_id)::text)))
  WHERE (cm.merchant_id IS NULL);


ALTER TABLE public.non_compliant_merchants OWNER TO axia;

--
-- Name: non_compliant_merchants_cs; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW non_compliant_merchants_cs AS
 SELECT se.id,
    se.merchant_id,
    se.merchant_email,
    se.merchant_name,
    se.password,
    se.email_sent,
    se.saq_type,
    se.first_scan_date,
    se.first_questionnaire_date,
    se.scan_status,
    se.questionnaire_status,
    se.pci_compliance,
    se.creation_date,
    se.sua,
    se.saq_completed_date,
    se.billing_date,
    se.next_billing_date,
    se.quarterly_scan_fee,
    se.compliance_fee,
    se.insurance_fee
   FROM saq_eligible se
  WHERE ((se.pci_compliance)::text = 'No'::text);


ALTER TABLE public.non_compliant_merchants_cs OWNER TO axia;

--
-- Name: non_compliant_merchants_cs_mv; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE non_compliant_merchants_cs_mv (
    id integer,
    merchant_id character varying(50),
    merchant_email character varying(100),
    merchant_name character varying(255),
    password character varying(255),
    email_sent timestamp without time zone,
    saq_type character varying(4),
    first_scan_date date,
    first_questionnaire_date date,
    scan_status character varying,
    questionnaire_status character varying,
    pci_compliance character varying(3),
    creation_date date,
    sua timestamp with time zone,
    saq_completed_date date,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone,
    quarterly_scan_fee double precision,
    compliance_fee double precision,
    insurance_fee double precision
);


ALTER TABLE public.non_compliant_merchants_cs_mv OWNER TO axia;

--
-- Name: non_compliant_merchants_mv; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE non_compliant_merchants_mv (
    id integer,
    merchant_id character varying(50),
    merchant_email character varying(100),
    merchant_name character varying(255),
    password character varying(255),
    email_sent timestamp without time zone,
    saq_type character varying(4),
    first_scan_date date,
    first_questionnaire_date date,
    scan_status character varying,
    questionnaire_status character varying,
    pci_compliance character varying(3),
    creation_date date,
    sua timestamp with time zone,
    saq_completed_date date,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone,
    quarterly_scan_fee double precision,
    compliance_fee double precision,
    insurance_fee double precision
);


ALTER TABLE public.non_compliant_merchants_mv OWNER TO axia;

--
-- Name: note_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE note_type (
    note_type character varying(10) NOT NULL,
    note_type_description character varying(50)
);


ALTER TABLE public.note_type OWNER TO axia;

--
-- Name: onlineapp_api_logs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_api_logs (
    id character varying(36) NOT NULL,
    user_id integer,
    user_token character varying(40) DEFAULT NULL::character varying,
    ip_address inet,
    request_string text,
    request_url text,
    request_type character varying(10) DEFAULT NULL::character varying,
    created timestamp without time zone,
    auth_status character varying(7) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_api_logs OWNER TO axia;

--
-- Name: onlineapp_apips; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_apips (
    id integer NOT NULL,
    user_id integer,
    ip_address inet
);


ALTER TABLE public.onlineapp_apips OWNER TO axia;

--
-- Name: onlineapp_apips_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_apips_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_apips_id_seq OWNER TO axia;

--
-- Name: onlineapp_apips_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_apips_id_seq OWNED BY onlineapp_apips.id;


--
-- Name: onlineapp_applications_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_applications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_applications_id_seq OWNER TO axia;

--
-- Name: onlineapp_applications; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_applications (
    id integer DEFAULT nextval('onlineapp_applications_id_seq'::regclass) NOT NULL,
    user_id integer NOT NULL,
    status character varying(10),
    hash character varying(32),
    rs_document_guid character varying(40),
    ownership_type character varying(255),
    legal_business_name character varying(255),
    mailing_address character varying(255),
    mailing_city character varying(255),
    mailing_state character varying(255),
    mailing_zip character varying(10),
    mailing_phone character varying(20),
    mailing_fax character varying(20),
    federal_taxid character varying(255),
    corp_contact_name character varying(255),
    corp_contact_name_title character varying(50) DEFAULT ''::character varying,
    corporate_email character varying(255),
    loc_same_as_corp boolean,
    dba_business_name character varying(255),
    location_address character varying(255),
    location_city character varying(255),
    location_state character varying(255),
    location_zip character varying(10),
    location_phone character varying(20),
    location_fax character varying(20),
    customer_svc_phone character varying(20),
    loc_contact_name character varying(255),
    loc_contact_name_title character varying(255),
    location_email character varying(255),
    website character varying(255),
    bus_open_date character varying(255),
    length_current_ownership character varying(255),
    existing_axia_merchant character varying(10),
    current_mid_number character varying(50),
    general_comments text,
    location_type character varying(255),
    location_type_other character varying(255),
    merchant_status character varying(255),
    landlord_name character varying(255),
    landlord_phone character varying(255),
    business_type character varying(50),
    products_services_sold character varying(255),
    return_policy character varying(255),
    days_until_prod_delivery character varying(255),
    monthly_volume character varying(50),
    average_ticket character varying(50),
    highest_ticket character varying(50),
    current_processor character varying(50),
    card_present_swiped smallint,
    card_present_imprint smallint,
    card_not_present_keyed smallint,
    card_not_present_internet smallint,
    method_total smallint,
    direct_to_customer smallint,
    direct_to_business smallint,
    direct_to_govt smallint,
    products_total smallint,
    high_volume_january boolean,
    high_volume_february boolean,
    high_volume_march boolean,
    high_volume_april boolean,
    high_volume_may boolean,
    high_volume_june boolean,
    high_volume_july boolean,
    high_volume_august boolean,
    high_volume_september boolean,
    high_volume_october boolean,
    high_volume_november boolean,
    high_volume_december boolean,
    moto_storefront_location character varying(10),
    moto_orders_at_location character varying(10),
    moto_inventory_housed character varying(255),
    moto_outsourced_customer_service boolean,
    moto_outsourced_shipment boolean,
    moto_outsourced_returns boolean,
    moto_outsourced_billing boolean,
    moto_sales_methods character varying(255),
    moto_billing_monthly boolean,
    moto_billing_quarterly boolean,
    moto_billing_semiannually boolean,
    moto_billing_annually boolean,
    moto_policy_full_up_front character varying(10),
    moto_policy_days_until_delivery character varying(10),
    moto_policy_partial_up_front character varying(10),
    moto_policy_partial_with character varying(10),
    moto_policy_days_until_final character varying(10),
    moto_policy_after character varying(10),
    bank_name character varying(255),
    bank_contact_name character varying(255),
    bank_phone character varying(20),
    bank_address character varying(255),
    bank_city character varying(255),
    bank_state character varying(255),
    bank_zip character varying(20),
    depository_routing_number character varying(255),
    depository_account_number character varying(255),
    same_as_depository boolean,
    fees_routing_number character varying(255),
    fees_account_number character varying(255),
    trade1_business_name character varying(255),
    trade1_contact_person character varying(255),
    trade1_phone character varying(20),
    trade1_acct_num character varying(255),
    trade1_city character varying(255),
    trade1_state character varying(255),
    trade2_business_name character varying(255),
    trade2_contact_person character varying(255),
    trade2_phone character varying(20),
    trade2_acct_num character varying(255),
    trade2_city character varying(255),
    trade2_state character varying(255),
    currently_accept_amex character varying(10),
    existing_se_num character varying(255),
    want_to_accept_amex character varying(10),
    want_to_accept_discover character varying(10),
    term1_quantity smallint,
    term1_type character varying(30) DEFAULT ''::character varying,
    term1_provider character varying(255),
    term1_use_autoclose character varying(10),
    term1_what_time character varying(255),
    term1_programming_avs boolean,
    term1_programming_server_nums boolean,
    term1_programming_tips boolean,
    term1_programming_invoice_num boolean,
    term1_programming_purchasing_cards boolean,
    term1_accept_debit character varying(10),
    term1_pin_pad_type character varying(255),
    term1_pin_pad_qty smallint,
    term2_quantity smallint,
    term2_type character varying(30) DEFAULT ''::character varying,
    term2_provider character varying(255),
    term2_use_autoclose character varying(10),
    term2_what_time character varying(255),
    term2_programming_avs boolean,
    term2_programming_server_nums boolean,
    term2_programming_tips boolean,
    term2_programming_invoice_num boolean,
    term2_programming_purchasing_cards boolean,
    term2_accept_debit character varying(10),
    term2_pin_pad_type character varying(255),
    term2_pin_pad_qty smallint,
    owner1_percentage smallint,
    owner1_fullname character varying(255),
    owner1_title character varying(255),
    owner1_address character varying(255),
    owner1_city character varying(255),
    owner1_state character varying(255),
    owner1_zip character varying(20),
    owner1_phone character varying(20),
    owner1_fax character varying(20),
    owner1_email character varying(255),
    owner1_ssn character varying(255),
    owner1_dob character varying(50),
    owner2_percentage smallint,
    owner2_fullname character varying(255),
    owner2_title character varying(255),
    owner2_address character varying(255),
    owner2_city character varying(255),
    owner2_state character varying(255),
    owner2_zip character varying(20),
    owner2_phone character varying(20),
    owner2_fax character varying(20),
    owner2_email character varying(255),
    owner2_ssn character varying(255),
    owner2_dob character varying(50),
    referral1_business character varying(255),
    referral1_owner_officer character varying(255),
    referral1_phone character varying(20),
    referral2_business character varying(255),
    referral2_owner_officer character varying(255),
    referral2_phone character varying(20),
    referral3_business character varying(255),
    referral3_owner_officer character varying(255),
    referral3_phone character varying(20),
    rep_contractor_name character varying(255),
    fees_rate_discount character varying(20),
    fees_rate_structure character varying(255),
    fees_qualification_exemptions character varying(255),
    fees_startup_application character varying(20),
    fees_auth_transaction character varying(20),
    fees_monthly_statement character varying(20),
    fees_misc_annual_file character varying(20),
    fees_startup_equipment character varying(20),
    fees_auth_amex character varying(20),
    fees_monthly_minimum character varying(20),
    fees_misc_chargeback character varying(20),
    fees_startup_expedite character varying(20),
    fees_auth_aru_voice character varying(20),
    fees_monthly_debit_access character varying(20),
    fees_startup_reprogramming character varying(20),
    fees_auth_wireless character varying(20),
    fees_monthly_ebt character varying(20),
    fees_startup_training character varying(20),
    fees_monthly_gateway_access character varying(20),
    fees_startup_wireless_activation character varying(20),
    fees_monthly_wireless_access character varying(20),
    fees_startup_tax character varying(20),
    fees_startup_total character varying(20),
    fees_pin_debit_auth character varying(20),
    fees_ebt_discount character varying(20),
    fees_pin_debit_discount character varying(20),
    fees_ebt_auth character varying(20),
    rep_discount_paid character varying(10),
    rep_amex_discount_rate character varying(20),
    rep_business_legitimate character varying(10),
    rep_photo_included character varying(10),
    rep_inventory_sufficient character varying(10),
    rep_goods_delivered character varying(10),
    rep_bus_open_operating character varying(10),
    rep_visa_mc_decals_visible character varying(10),
    rep_mail_tel_activity character varying(10),
    created timestamp(6) without time zone,
    modified timestamp(6) without time zone,
    moto_inventory_owned character varying(10),
    moto_outsourced_customer_service_field character varying(40),
    moto_outsourced_shipment_field character varying(40),
    moto_outsourced_returns_field character varying(40),
    moto_sales_local boolean,
    moto_sales_national boolean,
    site_survey_signature character varying(40),
    api integer DEFAULT 0 NOT NULL,
    var_status character varying(10),
    install_var_rs_document_guid character varying(32),
    tickler_id character varying(36),
    callback_url character varying(255) DEFAULT NULL::character varying,
    guid character varying(40) DEFAULT NULL::character varying,
    redirect_url character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_applications OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_aches; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_cobranded_application_aches (
    id integer NOT NULL,
    cobranded_application_id integer NOT NULL,
    description character varying(255) DEFAULT NULL::character varying,
    auth_type character varying(255) NOT NULL,
    routing_number character varying(255) NOT NULL,
    account_number character varying(255) NOT NULL,
    bank_name character varying(255) NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


ALTER TABLE public.onlineapp_cobranded_application_aches OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_aches_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_cobranded_application_aches_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobranded_application_aches_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_aches_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_cobranded_application_aches_id_seq OWNED BY onlineapp_cobranded_application_aches.id;


--
-- Name: onlineapp_cobranded_application_values; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_cobranded_application_values (
    id integer NOT NULL,
    cobranded_application_id integer NOT NULL,
    template_field_id integer NOT NULL,
    name character varying(255) NOT NULL,
    value character varying(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


ALTER TABLE public.onlineapp_cobranded_application_values OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_values_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_cobranded_application_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobranded_application_values_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_values_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_cobranded_application_values_id_seq OWNED BY onlineapp_cobranded_application_values.id;


--
-- Name: onlineapp_cobranded_applications; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_cobranded_applications (
    id integer NOT NULL,
    user_id integer,
    template_id integer,
    uuid character varying(36) NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    rightsignature_document_guid character varying(50) DEFAULT NULL::character varying,
    status character varying(10) DEFAULT NULL::character varying,
    rightsignature_install_document_guid character varying(50) DEFAULT NULL::character varying,
    rightsignature_install_status character varying(10) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_cobranded_applications OWNER TO axia;

--
-- Name: onlineapp_cobranded_applications_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_cobranded_applications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobranded_applications_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobranded_applications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_cobranded_applications_id_seq OWNED BY onlineapp_cobranded_applications.id;


--
-- Name: onlineapp_cobrands; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_cobrands (
    id integer NOT NULL,
    partner_name character varying(255) NOT NULL,
    partner_name_short character varying(255) NOT NULL,
    logo_url character varying(255) DEFAULT NULL::character varying,
    description text,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    response_url_type integer
);


ALTER TABLE public.onlineapp_cobrands OWNER TO axia;

--
-- Name: onlineapp_cobrands_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_cobrands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobrands_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobrands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_cobrands_id_seq OWNED BY onlineapp_cobrands.id;


--
-- Name: onlineapp_coversheets; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_coversheets (
    id integer NOT NULL,
    onlineapp_application_id integer,
    user_id integer NOT NULL,
    status character varying(10) NOT NULL,
    setup_existing_merchant boolean,
    setup_banking boolean,
    setup_statements boolean,
    setup_drivers_license boolean,
    setup_new_merchant boolean,
    setup_business_license boolean,
    setup_other boolean,
    setup_field_other character varying(20),
    setup_tier_select character varying(1),
    setup_tier3 boolean,
    setup_tier4 boolean,
    setup_tier5_financials boolean,
    setup_tier5_processing_statements boolean,
    setup_tier5_bank_statements boolean,
    setup_equipment_terminal boolean,
    setup_equipment_gateway boolean,
    setup_install character varying(10),
    setup_starterkit character varying(10),
    setup_equipment_payment character varying(10),
    setup_lease_price numeric(5,2),
    setup_lease_months numeric(2,0),
    setup_debit_volume numeric(12,2),
    setup_item_count numeric(7,0),
    setup_referrer character varying(20),
    setup_referrer_type character varying(2),
    setup_referrer_pct numeric(2,0),
    setup_reseller character varying(20),
    setup_reseller_type character varying(2),
    setup_reseller_pct numeric(2,0),
    setup_notes character varying(255),
    cp_encrypted_sn character varying(12),
    cp_pinpad_ra_attached boolean,
    cp_giftcards character varying(10),
    cp_check_guarantee character varying(10),
    cp_check_guarantee_info character varying(50),
    cp_pos character varying(10),
    cp_pos_contact character varying(50),
    micros character varying(10),
    micros_billing character varying(10),
    gateway_option character varying(10),
    gateway_package character varying(10),
    gateway_gold_subpackage character varying(10),
    gateway_epay character varying(10),
    gateway_billing character varying(10),
    moto_online_chd character varying(10),
    moto_developer character varying(40),
    moto_company character varying(40),
    moto_gateway character varying(40),
    moto_contact character varying(40),
    moto_phone character varying(40),
    moto_email character varying(40),
    created timestamp without time zone,
    modified timestamp without time zone,
    cobranded_application_id integer
);


ALTER TABLE public.onlineapp_coversheets OWNER TO axia;

--
-- Name: onlineapp_coversheets_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_coversheets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_coversheets_id_seq OWNER TO axia;

--
-- Name: onlineapp_coversheets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_coversheets_id_seq OWNED BY onlineapp_coversheets.id;


--
-- Name: onlineapp_email_timeline_subjects; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_email_timeline_subjects (
    id integer NOT NULL,
    subject character varying(40) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_email_timeline_subjects OWNER TO axia;

--
-- Name: onlineapp_email_timeline_subjects_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_email_timeline_subjects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_email_timeline_subjects_id_seq OWNER TO axia;

--
-- Name: onlineapp_email_timeline_subjects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_email_timeline_subjects_id_seq OWNED BY onlineapp_email_timeline_subjects.id;


--
-- Name: onlineapp_email_timelines; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_email_timelines (
    id integer NOT NULL,
    app_id integer,
    date timestamp without time zone,
    email_timeline_subject_id integer,
    recipient character varying(50),
    cobranded_application_id integer
);


ALTER TABLE public.onlineapp_email_timelines OWNER TO axia;

--
-- Name: onlineapp_email_timelines_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_email_timelines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_email_timelines_id_seq OWNER TO axia;

--
-- Name: onlineapp_email_timelines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_email_timelines_id_seq OWNED BY onlineapp_email_timelines.id;


--
-- Name: onlineapp_epayments; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_epayments (
    id integer NOT NULL,
    pin integer NOT NULL,
    application_id integer NOT NULL,
    merchant_id character varying(40),
    user_id integer,
    onlineapp_application_id integer,
    date_boarded timestamp without time zone NOT NULL,
    date_retrieved timestamp without time zone
);


ALTER TABLE public.onlineapp_epayments OWNER TO axia;

--
-- Name: onlineapp_epayments_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_epayments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_epayments_id_seq OWNER TO axia;

--
-- Name: onlineapp_epayments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_epayments_id_seq OWNED BY onlineapp_epayments.id;


--
-- Name: onlineapp_groups; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_groups (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp(6) without time zone,
    modified timestamp(6) without time zone
);


ALTER TABLE public.onlineapp_groups OWNER TO axia;

--
-- Name: onlineapp_groups_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_groups_id_seq OWNER TO axia;

--
-- Name: onlineapp_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_groups_id_seq OWNED BY onlineapp_groups.id;


--
-- Name: onlineapp_multipasses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_multipasses (
    id character varying(36) NOT NULL,
    merchant_id character varying(16),
    device_number character varying(14),
    username character varying(20),
    pass character varying(20),
    in_use boolean DEFAULT false NOT NULL,
    application_id integer,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.onlineapp_multipasses OWNER TO axia;

--
-- Name: onlineapp_settings; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_settings (
    key character varying(255) NOT NULL,
    value character varying(255),
    description text
);


ALTER TABLE public.onlineapp_settings OWNER TO axia;

--
-- Name: onlineapp_template_fields; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_template_fields (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    rep_only boolean DEFAULT false NOT NULL,
    width integer DEFAULT 12 NOT NULL,
    type integer NOT NULL,
    required boolean DEFAULT false NOT NULL,
    source integer NOT NULL,
    default_value text,
    merge_field_name character varying(255) DEFAULT NULL::character varying,
    "order" integer NOT NULL,
    section_id integer NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    encrypt boolean DEFAULT false NOT NULL
);


ALTER TABLE public.onlineapp_template_fields OWNER TO axia;

--
-- Name: onlineapp_template_fields_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_template_fields_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_template_fields_id_seq OWNER TO axia;

--
-- Name: onlineapp_template_fields_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_template_fields_id_seq OWNED BY onlineapp_template_fields.id;


--
-- Name: onlineapp_template_pages; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_template_pages (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    rep_only boolean DEFAULT false NOT NULL,
    template_id integer NOT NULL,
    "order" integer NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


ALTER TABLE public.onlineapp_template_pages OWNER TO axia;

--
-- Name: onlineapp_template_pages_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_template_pages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_template_pages_id_seq OWNER TO axia;

--
-- Name: onlineapp_template_pages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_template_pages_id_seq OWNED BY onlineapp_template_pages.id;


--
-- Name: onlineapp_template_sections; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_template_sections (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    rep_only boolean DEFAULT false NOT NULL,
    width integer DEFAULT 12 NOT NULL,
    page_id integer NOT NULL,
    "order" integer NOT NULL,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


ALTER TABLE public.onlineapp_template_sections OWNER TO axia;

--
-- Name: onlineapp_template_sections_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_template_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_template_sections_id_seq OWNER TO axia;

--
-- Name: onlineapp_template_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_template_sections_id_seq OWNED BY onlineapp_template_sections.id;


--
-- Name: onlineapp_templates; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_templates (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    logo_position integer DEFAULT 3 NOT NULL,
    include_axia_logo boolean DEFAULT true NOT NULL,
    description text,
    cobrand_id integer,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    rightsignature_template_guid character varying(50) DEFAULT NULL::character varying,
    rightsignature_install_template_guid character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_templates OWNER TO axia;

--
-- Name: onlineapp_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_templates_id_seq OWNER TO axia;

--
-- Name: onlineapp_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_templates_id_seq OWNED BY onlineapp_templates.id;


--
-- Name: onlineapp_users; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_users (
    id integer NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(40) NOT NULL,
    group_id integer NOT NULL,
    created timestamp(6) without time zone NOT NULL,
    modified timestamp(6) without time zone NOT NULL,
    token character(40) DEFAULT NULL::bpchar,
    token_used timestamp without time zone,
    token_uses integer DEFAULT 0 NOT NULL,
    firstname character varying(40),
    lastname character varying(40),
    extension integer,
    active boolean DEFAULT true,
    api_password character varying(50),
    api_enabled boolean,
    cobrand_id integer,
    template_id integer
);


ALTER TABLE public.onlineapp_users OWNER TO axia;

--
-- Name: onlineapp_users_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE onlineapp_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_users_id_seq OWNER TO axia;

--
-- Name: onlineapp_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE onlineapp_users_id_seq OWNED BY onlineapp_users.id;


--
-- Name: onlineapp_users_managers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_users_managers (
    id character(36) NOT NULL,
    user_id integer,
    manager_id integer
);


ALTER TABLE public.onlineapp_users_managers OWNER TO axia;

--
-- Name: orderitem_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orderitem_type (
    orderitem_type_id integer NOT NULL,
    orderitem_type_description character varying(55)
);


ALTER TABLE public.orderitem_type OWNER TO axia;

--
-- Name: orderitems; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orderitems (
    orderitem_id integer NOT NULL,
    order_id integer NOT NULL,
    equipment_type character varying(10) NOT NULL,
    equipment_item_description character varying(100),
    quantity integer,
    equipment_item_true_price real,
    equipment_item_rep_price real,
    equipment_item integer,
    hardware_sn character varying(20),
    hardware_replacement_for character varying(20),
    warranty integer,
    item_merchant_id character varying(50),
    item_tax real,
    item_ship_type integer,
    item_ship_cost real,
    item_commission_month character varying(8),
    item_ach_seq integer,
    item_date_ordered date,
    type_id integer
);


ALTER TABLE public.orderitems OWNER TO axia;

--
-- Name: orderitems_replacement; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orderitems_replacement (
    orderitem_replacement_id integer NOT NULL,
    orderitem_id integer NOT NULL,
    shipping_axia_to_merchant_id integer,
    shipping_axia_to_merchant_cost real,
    shipping_merchant_to_vendor_id integer,
    shipping_merchant_to_vendor_cos real,
    shipping_vendor_to_axia_id integer,
    shipping_vendor_to_axia_cost real,
    ra_num integer,
    tracking_num_old integer,
    date_shipped_to_vendor date,
    date_arrived_from_vendor date,
    amount_billed_to_merchant real,
    tracking_num character varying(25)
);


ALTER TABLE public.orderitems_replacement OWNER TO axia;

--
-- Name: orders; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orders (
    order_id integer NOT NULL,
    status character varying(5) NOT NULL,
    user_id integer NOT NULL,
    date_ordered date,
    date_paid date,
    merchant_id character varying(50),
    shipping_cost real,
    tax real,
    ship_to character varying(50),
    tracking_number character varying(50),
    notes text,
    invoice_number integer,
    shipping_type integer,
    display_id integer,
    ach_seq_number integer,
    commission_month character varying(8),
    vendor_id integer,
    add_item_tax integer,
    commission_month_nocharge integer
);


ALTER TABLE public.orders OWNER TO axia;

--
-- Name: partner; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE partner (
    partner_id integer NOT NULL,
    partner_name character varying(255) NOT NULL,
    active integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.partner OWNER TO axia;

--
-- Name: pci_billing; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_billing (
    id integer NOT NULL,
    pci_billing_type_id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    billing_date date
);


ALTER TABLE public.pci_billing OWNER TO axia;

--
-- Name: pci_billing_history; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_billing_history (
    id integer NOT NULL,
    pci_billing_type_id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    billing_date date,
    date_change date NOT NULL,
    operation character varying(25) NOT NULL
);


ALTER TABLE public.pci_billing_history OWNER TO axia;

--
-- Name: pci_billing_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE pci_billing_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pci_billing_id_seq OWNER TO axia;

--
-- Name: pci_billing_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE pci_billing_id_seq OWNED BY pci_billing.id;


--
-- Name: pci_billing_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_billing_type (
    id integer NOT NULL,
    name character varying(255)
);


ALTER TABLE public.pci_billing_type OWNER TO axia;

--
-- Name: pci_billing_type_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE pci_billing_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pci_billing_type_id_seq OWNER TO axia;

--
-- Name: pci_billing_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE pci_billing_type_id_seq OWNED BY pci_billing_type.id;


--
-- Name: pci_compliance; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_compliance (
    id integer NOT NULL,
    pci_compliance_date_type_id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    date_complete date NOT NULL
);


ALTER TABLE public.pci_compliance OWNER TO axia;

--
-- Name: pci_compliance_date_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_compliance_date_type (
    id integer NOT NULL,
    name character varying(255)
);


ALTER TABLE public.pci_compliance_date_type OWNER TO axia;

--
-- Name: pci_compliance_date_type_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE pci_compliance_date_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pci_compliance_date_type_id_seq OWNER TO axia;

--
-- Name: pci_compliance_date_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE pci_compliance_date_type_id_seq OWNED BY pci_compliance_date_type.id;


--
-- Name: pci_compliance_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE pci_compliance_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pci_compliance_id_seq OWNER TO axia;

--
-- Name: pci_compliance_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE pci_compliance_id_seq OWNED BY pci_compliance.id;


--
-- Name: pci_compliance_status_log; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_compliance_status_log (
    pci_compliance_date_type_id integer,
    saq_merchant_id integer,
    date_complete date,
    date_change timestamp without time zone,
    operation character varying(25)
);


ALTER TABLE public.pci_compliance_status_log OWNER TO axia;

--
-- Name: pci_reminders; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW pci_reminders AS
 SELECT sm.id,
    sm.merchant_id,
    ( SELECT (max(pc_1.date_complete) + '1 year'::interval)
           FROM pci_compliance pc_1
          WHERE ((pc_1.saq_merchant_id = sm.id) AND (pc_1.pci_compliance_date_type_id = ANY (ARRAY[1, 2, 4])))) AS saq_deadline,
    ( SELECT (max(pc_1.date_complete) + '3 mons'::interval)
           FROM pci_compliance pc_1
          WHERE ((pc_1.saq_merchant_id = sm.id) AND (pc_1.pci_compliance_date_type_id = ANY (ARRAY[3, 5])))) AS scan_deadline,
    ( SELECT
                CASE
                    WHEN ((max(pc_1.date_complete) + '1 year'::interval) < now()) THEN 'SAQ EXPIRED'::text
                    WHEN ((max(pc_1.date_complete) + '1 year'::interval) < (now() + '1 mon'::interval)) THEN 'SEND SAQ REMINDER'::text
                    ELSE (date_part('day'::text, (((max(pc_1.date_complete) + '1 year'::interval))::timestamp with time zone - now())) || ' Days Left'::text)
                END AS "case"
           FROM pci_compliance pc_1
          WHERE ((pc_1.saq_merchant_id = sm.id) AND (pc_1.pci_compliance_date_type_id = ANY (ARRAY[1, 2, 4])))) AS saq_status,
    ( SELECT
                CASE
                    WHEN ((max(pc_1.date_complete) + '3 mons'::interval) < now()) THEN 'SCAN EXPIRED'::text
                    WHEN ((max(pc_1.date_complete) + '3 mons'::interval) < (now() + '20 days'::interval)) THEN 'SEND SCAN REMINDER'::text
                    ELSE (date_part('day'::text, (((max(pc_1.date_complete) + '3 mons'::interval))::timestamp with time zone - now())) || ' Days Left'::text)
                END AS "case"
           FROM pci_compliance pc_1
          WHERE ((pc_1.saq_merchant_id = sm.id) AND (pc_1.pci_compliance_date_type_id = ANY (ARRAY[3, 5])))) AS scan_status,
    ( SELECT pc_1.pci_compliance_date_type_id
           FROM pci_compliance pc_1
          WHERE ((pc_1.pci_compliance_date_type_id = ANY (ARRAY[1, 2, 4])) AND (pc_1.saq_merchant_id = sm.id))
          ORDER BY pc_1.date_complete DESC
         LIMIT 1) AS saq_date_type_id,
    ( SELECT pc_1.pci_compliance_date_type_id
           FROM pci_compliance pc_1
          WHERE ((pc_1.pci_compliance_date_type_id = ANY (ARRAY[3, 5])) AND (pc_1.saq_merchant_id = sm.id))
          ORDER BY pc_1.date_complete DESC
         LIMIT 1) AS scan_date_type_id
   FROM ((saq_merchant sm
     LEFT JOIN pci_compliance pc ON ((sm.id = pc.saq_merchant_id)))
     JOIN pci_compliance_date_type pcdt ON ((pc.pci_compliance_date_type_id = pcdt.id)))
  GROUP BY sm.id, sm.merchant_id
  ORDER BY sm.id DESC;


ALTER TABLE public.pci_reminders OWNER TO axia;

--
-- Name: pci_reminders_mv; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_reminders_mv (
    id integer,
    merchant_id character varying(50),
    saq_deadline timestamp without time zone,
    scan_deadline timestamp without time zone,
    saq_status text,
    scan_status text,
    saq_date_type_id integer,
    scan_date_type_id integer
);


ALTER TABLE public.pci_reminders_mv OWNER TO axia;

--
-- Name: permission; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE permission (
    permission_id integer NOT NULL,
    permission_group character varying(20) NOT NULL,
    permission_description character varying(100)
);


ALTER TABLE public.permission OWNER TO axia;

--
-- Name: permission_group; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE permission_group (
    permission_group character varying(20) NOT NULL,
    permission_group_description character varying(50)
);


ALTER TABLE public.permission_group OWNER TO axia;

--
-- Name: pricing_matrix; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pricing_matrix (
    matrix_id integer NOT NULL,
    user_id integer NOT NULL,
    user_type character varying(10) NOT NULL,
    matrix_profit_perc real,
    matrix_new_monthly_volume real,
    matrix_new_monthly_profit real,
    matrix_view_conjunction character varying(10),
    matrix_total_volume real,
    matrix_total_accounts real,
    matrix_total_profit real,
    matrix_draw real,
    matrix_new_accounts_min real,
    matrix_new_accounts_max real
);


ALTER TABLE public.pricing_matrix OWNER TO axia;

--
-- Name: products_and_services; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE products_and_services (
    merchant_id character varying(50) NOT NULL,
    products_services_type character varying(10) NOT NULL
);


ALTER TABLE public.products_and_services OWNER TO axia;

--
-- Name: products_services_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE products_services_type (
    products_services_type character varying(10) NOT NULL,
    products_services_description character varying(50),
    products_services_rppp boolean DEFAULT true
);


ALTER TABLE public.products_services_type OWNER TO axia;

--
-- Name: referer_products_services_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE referer_products_services_xref (
    ref_seq_number integer NOT NULL,
    products_services_type character varying(10) NOT NULL
);


ALTER TABLE public.referer_products_services_xref OWNER TO axia;

--
-- Name: referers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE referers (
    ref_seq_number integer NOT NULL,
    ref_name character varying(50),
    ref_ref_perc real,
    active integer,
    ref_username character varying(50),
    ref_password character varying(50),
    ref_residuals boolean DEFAULT false NOT NULL,
    ref_commissions boolean DEFAULT false NOT NULL,
    is_referer boolean DEFAULT true NOT NULL
);


ALTER TABLE public.referers OWNER TO axia;

--
-- Name: referers_bet; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE referers_bet (
    referers_bet_id integer NOT NULL,
    ref_seq_number integer NOT NULL,
    bet_code character varying(50) NOT NULL,
    pct real DEFAULT 0 NOT NULL
);


ALTER TABLE public.referers_bet OWNER TO axia;

--
-- Name: saq_merchant_pci_email_sent; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchant_pci_email_sent (
    id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    saq_merchant_pci_email_id integer NOT NULL,
    date_sent timestamp without time zone NOT NULL
);


ALTER TABLE public.saq_merchant_pci_email_sent OWNER TO axia;

--
-- Name: reminder; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW reminder AS
 SELECT DISTINCT saq_merchant_pci_email_sent.saq_merchant_id,
    count(saq_merchant_pci_email_sent.saq_merchant_id) AS count
   FROM saq_merchant_pci_email_sent
  WHERE (saq_merchant_pci_email_sent.date_sent < '2011-09-01 00:00:00'::timestamp without time zone)
  GROUP BY saq_merchant_pci_email_sent.saq_merchant_id
 HAVING (count(saq_merchant_pci_email_sent.saq_merchant_id) < 2);


ALTER TABLE public.reminder OWNER TO axia;

--
-- Name: rep_cost_structure; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE rep_cost_structure (
    user_id integer NOT NULL,
    debit_monthly_fee real,
    debit_per_item_fee real,
    gift_statement_fee real,
    gift_magstripe_item_fee real,
    gift_magstripe_loyalty_fee real,
    gift_chipcard_item_fee real,
    gift_chipcard_loyalty_fee real,
    gift_chipcard_onerate_fee real,
    cg_volume real,
    vc_web_based_rate real,
    vc_web_based_pi real,
    vc_monthly_fee real,
    vc_gateway_fee real,
    ach_merchant_based real,
    ach_file_fee real,
    ach_eft_ccd_nw real,
    ach_eft_ccd_w real,
    ach_eft_ppd_nw real,
    ach_eft_ppd_w real,
    ach_eft_rck real,
    ach_reject_fee real,
    ach_statement_fee real,
    ach_secure_gateway_eft real,
    ebt_per_item_fee real,
    ebt_monthly_fee real,
    main_statement_fee real,
    main_vrt_term_gwy_fee real,
    vc_web_based_monthly_fee real,
    vc_web_based_gateway_fee real,
    te_transaction_fee real,
    af_annual_fee real,
    multiple real,
    auth_monthly_fee real,
    auth_per_item_fee real,
    debit_rate_rep_cost_pct real,
    ach_rate real,
    gw_rate real,
    gw_per_item real,
    debit_per_item_fee_tsys real,
    debit_rate_rep_cost_pct_tsys real,
    debit_per_item_fee_tsys_new real,
    debit_rate_rep_cost_pct_tsys_new real,
    discover_statement_fee real,
    debit_per_item_fee_directconnect real,
    debit_rate_rep_cost_pct_directconnect real,
    debit_per_item_fee_sagepti real,
    debit_rate_rep_cost_pct_sagepti real,
    tg_rate real,
    tg_per_item real,
    wp_rate real,
    wp_per_item real,
    tg_statement_fee real
);


ALTER TABLE public.rep_cost_structure OWNER TO axia;

--
-- Name: rep_partner_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE rep_partner_xref (
    user_id integer NOT NULL,
    partner_id integer NOT NULL,
    mgr1_id integer,
    mgr2_id integer,
    profit_pct real,
    multiple numeric(4,2),
    mgr1_profit_pct real,
    mgr2_profit_pct real
);


ALTER TABLE public.rep_partner_xref OWNER TO axia;

--
-- Name: rep_product_profit_pct; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE rep_product_profit_pct (
    user_id integer NOT NULL,
    products_services_type character varying(10) NOT NULL,
    pct real,
    multiple real,
    pct_gross real,
    do_not_display boolean DEFAULT false NOT NULL
);


ALTER TABLE public.rep_product_profit_pct OWNER TO axia;

--
-- Name: residual_pricing; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE residual_pricing (
    merchant_id character varying(50) NOT NULL,
    products_services_type character varying(10) NOT NULL,
    user_id integer NOT NULL,
    r_network character varying(50),
    r_month real,
    r_year real,
    r_rate_pct real,
    r_per_item_fee real,
    r_statement_fee real,
    m_rate_pct real,
    m_per_item_fee real,
    m_statement_fee real,
    refer_profit_pct real,
    ref_seq_number integer,
    m_hidden_per_item_fee real,
    m_hidden_per_item_fee_cross real,
    m_eft_secure_flag integer,
    m_gc_plan character varying(8),
    m_pos_partner_flag integer,
    bet_code character varying(50),
    bet_extra_pct real,
    pct_volume boolean DEFAULT false,
    res_profit_pct real,
    res_seq_number integer,
    m_micros_ip_flag integer,
    m_micros_dialup_flag integer,
    m_micros_per_item_fee real,
    m_wireless_flag integer,
    m_wireless_terminals integer,
    r_usaepay_flag integer,
    r_epay_retail_flag integer,
    r_usaepay_gtwy_cost real,
    r_usaepay_gtwy_add_cost real,
    m_tgate_flag integer,
    m_petro_flag integer,
    ref_p_type pp_types,
    ref_p_value real,
    res_p_type pp_types,
    res_p_value real,
    r_risk_assessment real,
    ref_p_pct integer,
    res_p_pct integer
);


ALTER TABLE public.residual_pricing OWNER TO axia;

--
-- Name: residual_report; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE residual_report (
    merchant_id character varying(50) NOT NULL,
    products_services_type character varying(10) NOT NULL,
    user_id integer NOT NULL,
    r_network character varying(50),
    r_month real,
    r_year real,
    r_rate_pct real,
    r_per_item_fee real,
    r_statement_fee real,
    r_profit_pct real,
    r_profit_amount real,
    m_rate_pct real,
    m_per_item_fee real,
    refer_profit_pct real,
    refer_profit_amount real,
    ref_seq_number integer,
    status character varying(5),
    m_statement_fee real,
    r_avg_ticket double precision,
    r_items double precision,
    r_volume double precision,
    total_profit double precision,
    manager_id integer,
    manager_profit_pct double precision,
    manager_profit_amount double precision,
    bet_code character varying(50),
    bet_extra_pct real,
    res_profit_pct real,
    res_profit_amount real,
    res_seq_number integer,
    manager_id_secondary integer,
    manager_profit_pct_secondary double precision,
    manager_profit_amount_secondary double precision,
    ref_p_type pp_types,
    ref_p_value real,
    res_p_type pp_types,
    res_p_value real,
    ref_p_pct integer,
    res_p_pct integer,
    partner_id integer,
    partner_exclude_volume boolean
);


ALTER TABLE public.residual_report OWNER TO axia;

--
-- Name: sales_goal; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE sales_goal (
    user_id integer NOT NULL,
    goal_accounts double precision,
    goal_volume double precision,
    goal_profits double precision,
    goal_statements double precision,
    goal_calls double precision,
    goal_month integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.sales_goal OWNER TO axia;

--
-- Name: sales_goal_archive; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE sales_goal_archive (
    user_id integer NOT NULL,
    goal_month integer,
    goal_year integer,
    goal_accounts integer,
    goal_volume double precision,
    goal_profits double precision,
    actual_accounts double precision,
    actual_volume double precision,
    actual_profits double precision,
    goal_statements double precision,
    goal_calls double precision,
    actual_statements double precision,
    actual_calls double precision
);


ALTER TABLE public.sales_goal_archive OWNER TO axia;

--
-- Name: saq_answer; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_answer (
    id integer NOT NULL,
    saq_merchant_survey_xref_id integer NOT NULL,
    saq_survey_question_xref_id integer NOT NULL,
    answer boolean NOT NULL,
    date timestamp without time zone NOT NULL
);


ALTER TABLE public.saq_answer OWNER TO axia;

--
-- Name: saq_answer_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE saq_answer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saq_answer_id_seq OWNER TO axia;

--
-- Name: saq_control_scan_unboarded; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_control_scan_unboarded (
    id integer NOT NULL,
    merchant_id character varying(50),
    date_unboarded date
);


ALTER TABLE public.saq_control_scan_unboarded OWNER TO axia;

--
-- Name: saq_control_scan_unboarded_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE saq_control_scan_unboarded_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saq_control_scan_unboarded_id_seq OWNER TO axia;

--
-- Name: saq_control_scan_unboarded_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE saq_control_scan_unboarded_id_seq OWNED BY saq_control_scan_unboarded.id;


--
-- Name: saq_eligible_mv; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_eligible_mv (
    id integer,
    merchant_id character varying(50),
    merchant_email character varying(100),
    merchant_name character varying(255),
    password character varying(255),
    email_sent timestamp without time zone,
    saq_type character varying(4),
    first_scan_date date,
    first_questionnaire_date date,
    scan_status character varying,
    questionnaire_status character varying,
    pci_compliance character varying(3),
    creation_date date,
    sua timestamp with time zone,
    saq_completed_date date,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone,
    quarterly_scan_fee double precision,
    compliance_fee double precision,
    insurance_fee double precision
);


ALTER TABLE public.saq_eligible_mv OWNER TO axia;

--
-- Name: saq_eligible_wrong; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW saq_eligible_wrong AS
 SELECT sm.id,
    sm.merchant_id,
    sm.merchant_email,
    sm.merchant_name,
    sm.password,
    sm.email_sent,
    scs.saq_type,
    scs.first_scan_date,
    scs.first_questionnaire_date,
    scs.scan_status,
    scs.questionnaire_status,
    scs.pci_compliance,
    scs.creation_date,
    scs.sua,
    mp.saq_completed_date,
    sm.billing_date,
    sm.next_billing_date,
    scs.quarterly_scan_fee,
    mp.compliance_fee,
    mp.insurance_fee
   FROM (((((((saq_merchant sm
     LEFT JOIN merchant m ON ((((sm.merchant_id)::text = (m.merchant_id)::text) AND (m.active = 1))))
     LEFT JOIN merchant_pci mp ON (((sm.merchant_id)::text = (mp.merchant_id)::text)))
     LEFT JOIN saq_control_scan scs ON ((substr((sm.merchant_id)::text, 5, 12) = substr((scs.merchant_id)::text, 5, 12))))
     LEFT JOIN merchant_cancellation mc ON ((((sm.merchant_id)::text = (mc.merchant_id)::text) AND (mc.date_completed IS NOT NULL))))
     LEFT JOIN timeline_entries tes ON ((((sm.merchant_id)::text = (tes.merchant_id)::text) AND (((tes.timeline_item)::text = 'SUB'::text) AND (tes.timeline_date_completed < '2011-08-01'::date)))))
     LEFT JOIN timeline_entries tei ON ((((sm.merchant_id)::text = (tei.merchant_id)::text) AND ((tei.timeline_item)::text = 'INS'::text))))
     LEFT JOIN bankcard b ON (((sm.merchant_id)::text = (b.merchant_id)::text)))
  WHERE ((((((sm.merchant_email)::text ~~ '%@%'::text) AND (m.merchant_id IS NOT NULL)) AND ((tei.merchant_id IS NOT NULL) OR (tes.merchant_id IS NOT NULL))) AND (b.merchant_id IS NOT NULL)) AND (mc.merchant_id IS NULL));


ALTER TABLE public.saq_eligible_wrong OWNER TO axia;

--
-- Name: saq_merchant_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE saq_merchant_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saq_merchant_id_seq OWNER TO axia;

--
-- Name: saq_merchant_pci_email; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchant_pci_email (
    id integer NOT NULL,
    priority integer NOT NULL,
    "interval" integer NOT NULL,
    title character varying(255) NOT NULL,
    filename_prefix character varying(255) NOT NULL,
    visible boolean DEFAULT true NOT NULL
);


ALTER TABLE public.saq_merchant_pci_email OWNER TO axia;

--
-- Name: saq_merchant_pci_email_sent_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE saq_merchant_pci_email_sent_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saq_merchant_pci_email_sent_id_seq OWNER TO axia;

--
-- Name: saq_merchant_survey_xref_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE saq_merchant_survey_xref_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saq_merchant_survey_xref_id_seq OWNER TO axia;

--
-- Name: saq_prequalification; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_prequalification (
    id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    result character varying(4) NOT NULL,
    date_completed timestamp without time zone NOT NULL,
    control_scan_code integer,
    control_scan_message text
);


ALTER TABLE public.saq_prequalification OWNER TO axia;

--
-- Name: saq_prequalification_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE saq_prequalification_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.saq_prequalification_id_seq OWNER TO axia;

--
-- Name: saq_question; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_question (
    id integer NOT NULL,
    question text NOT NULL
);


ALTER TABLE public.saq_question OWNER TO axia;

--
-- Name: saq_survey_question_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_survey_question_xref (
    id integer NOT NULL,
    saq_survey_id integer NOT NULL,
    saq_question_id integer NOT NULL,
    priority integer NOT NULL
);


ALTER TABLE public.saq_survey_question_xref OWNER TO axia;

--
-- Name: schema_migrations; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE schema_migrations (
    id integer NOT NULL,
    class character varying(255) NOT NULL,
    type character varying(50) NOT NULL,
    created timestamp without time zone NOT NULL
);


ALTER TABLE public.schema_migrations OWNER TO axia;

--
-- Name: schema_migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE schema_migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.schema_migrations_id_seq OWNER TO axia;

--
-- Name: schema_migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE schema_migrations_id_seq OWNED BY schema_migrations.id;


--
-- Name: shipping_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE shipping_type (
    shipping_type integer NOT NULL,
    shipping_type_description character varying(50) NOT NULL
);


ALTER TABLE public.shipping_type OWNER TO axia;

--
-- Name: shipping_type_item; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE shipping_type_item (
    shipping_type integer NOT NULL,
    shipping_type_description character varying(50) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.shipping_type_item OWNER TO axia;

--
-- Name: system_transaction; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE system_transaction (
    system_transaction_id integer NOT NULL,
    transaction_type character varying(10) NOT NULL,
    user_id integer NOT NULL,
    merchant_id character varying(50),
    session_id character varying(50),
    client_address character varying(100),
    system_transaction_date date,
    system_transaction_time time without time zone,
    merchant_note_id integer,
    change_id integer,
    ach_seq_number integer,
    order_id integer,
    programming_id integer
);


ALTER TABLE public.system_transaction OWNER TO axia;

--
-- Name: tgate; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tgate (
    merchant_id character varying(50) NOT NULL,
    tg_rate real,
    tg_per_item real,
    tg_statement real
);


ALTER TABLE public.tgate OWNER TO axia;

--
-- Name: tickler_availabilities; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_availabilities (
    id character varying(36) NOT NULL,
    name character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.tickler_availabilities OWNER TO axia;

--
-- Name: tickler_availabilities_leads; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_availabilities_leads (
    id character varying(36) NOT NULL,
    availability_id character varying(36) DEFAULT NULL::character varying,
    lead_id character varying(36) DEFAULT NULL::character varying
);


ALTER TABLE public.tickler_availabilities_leads OWNER TO axia;

--
-- Name: tickler_comments; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_comments (
    id character varying(36) NOT NULL,
    parent_id character varying(36) DEFAULT NULL::character varying,
    foreign_key character varying(36) NOT NULL,
    user_id integer,
    lft integer NOT NULL,
    rght integer NOT NULL,
    model character varying(255) NOT NULL,
    approved boolean DEFAULT true NOT NULL,
    is_spam character varying(20) DEFAULT 'clean'::character varying NOT NULL,
    title character varying(255) DEFAULT NULL::character varying,
    slug character varying(255) DEFAULT NULL::character varying,
    body text,
    author_name character varying(255) DEFAULT NULL::character varying,
    author_url character varying(255) DEFAULT NULL::character varying,
    author_email character varying(128) DEFAULT NULL::character varying,
    language character varying(6) DEFAULT NULL::character varying,
    comment_type character varying(32) DEFAULT 'comment'::character varying NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_comments OWNER TO axia;

--
-- Name: tickler_companies; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_companies (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_companies OWNER TO axia;

--
-- Name: tickler_equipments; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_equipments (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_equipments OWNER TO axia;

--
-- Name: tickler_followups; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_followups (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_followups OWNER TO axia;

--
-- Name: tickler_leads; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_leads (
    id character varying(36) NOT NULL,
    business_name character varying(100) NOT NULL,
    business_address character varying(100) NOT NULL,
    city character varying(60) NOT NULL,
    state_id character varying(36) DEFAULT NULL::character varying,
    zip character varying(12),
    phone character varying(20) DEFAULT NULL::character varying,
    mobile character varying(20) DEFAULT NULL::character varying,
    fax character varying(20) DEFAULT NULL::character varying,
    decision_maker character varying(50) DEFAULT NULL::character varying,
    decision_maker_title character varying(50),
    other_contact character varying(50) DEFAULT NULL::character varying,
    other_contact_title character varying(50),
    email character varying(100) NOT NULL,
    website character varying(2008) DEFAULT ''::character varying NOT NULL,
    company_id character varying(36) DEFAULT NULL::character varying,
    equipment_id character varying(36) DEFAULT NULL::character varying,
    referer_id character varying(36) DEFAULT NULL::character varying,
    reseller_id character varying(36) DEFAULT NULL::character varying,
    user_id integer,
    status_id character varying(36) DEFAULT NULL::character varying,
    likes1 character varying(100) DEFAULT NULL::character varying,
    likes2 character varying(100) DEFAULT NULL::character varying,
    likes3 character varying(100) DEFAULT NULL::character varying,
    dislikes1 character varying(100) DEFAULT NULL::character varying,
    dislikes2 character varying(100) DEFAULT NULL::character varying,
    dislikes3 character varying(100) DEFAULT NULL::character varying,
    created timestamp without time zone,
    modified timestamp without time zone,
    call_date date,
    meeting_date date,
    sign_date date,
    app_created integer,
    nlp_id integer,
    volume double precision,
    lat double precision,
    lng double precision
);


ALTER TABLE public.tickler_leads OWNER TO axia;

--
-- Name: tickler_leads_logs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_leads_logs (
    id character varying(36) NOT NULL,
    action character varying(10) DEFAULT 'add'::character varying NOT NULL,
    lead_id character varying(36) NOT NULL,
    status_id character varying(36) NOT NULL,
    created timestamp without time zone
);


ALTER TABLE public.tickler_leads_logs OWNER TO axia;

--
-- Name: tickler_loggable_logs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_loggable_logs (
    id integer NOT NULL,
    action character varying(150) NOT NULL,
    model character varying(150) NOT NULL,
    foreign_key character varying(150) NOT NULL,
    source_id integer NOT NULL,
    content text NOT NULL,
    created timestamp without time zone NOT NULL
);


ALTER TABLE public.tickler_loggable_logs OWNER TO axia;

--
-- Name: tickler_loggable_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE tickler_loggable_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tickler_loggable_logs_id_seq OWNER TO axia;

--
-- Name: tickler_loggable_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE tickler_loggable_logs_id_seq OWNED BY tickler_loggable_logs.id;


--
-- Name: tickler_referers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_referers (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_referers OWNER TO axia;

--
-- Name: tickler_resellers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_resellers (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_resellers OWNER TO axia;

--
-- Name: tickler_schema_migrations; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_schema_migrations (
    id integer NOT NULL,
    class character varying(255) NOT NULL,
    type character varying(50) NOT NULL,
    created timestamp without time zone NOT NULL
);


ALTER TABLE public.tickler_schema_migrations OWNER TO axia;

--
-- Name: tickler_schema_migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE tickler_schema_migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tickler_schema_migrations_id_seq OWNER TO axia;

--
-- Name: tickler_schema_migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE tickler_schema_migrations_id_seq OWNED BY tickler_schema_migrations.id;


--
-- Name: tickler_states; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_states (
    id character varying(36) NOT NULL,
    name character varying(2) NOT NULL,
    state character varying(30) DEFAULT NULL::character varying
);


ALTER TABLE public.tickler_states OWNER TO axia;

--
-- Name: tickler_statuses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tickler_statuses (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone,
    "order" integer
);


ALTER TABLE public.tickler_statuses OWNER TO axia;

--
-- Name: timeline_item; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE timeline_item (
    timeline_item character varying(50) NOT NULL,
    timeline_item_description character varying(100)
);


ALTER TABLE public.timeline_item OWNER TO axia;

--
-- Name: transaction_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE transaction_type (
    transaction_type character varying(10) NOT NULL,
    transaction_type_description character varying(50)
);


ALTER TABLE public.transaction_type OWNER TO axia;

--
-- Name: tsys_quarterly_scan_fees; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tsys_quarterly_scan_fees (
    merchant_id character varying(20),
    dba character varying(50),
    assoc_id integer,
    billing_method character varying(10),
    description character varying(50),
    type character varying(50),
    unit integer,
    amount character varying(10),
    search_type character varying(20)
);


ALTER TABLE public.tsys_quarterly_scan_fees OWNER TO axia;

--
-- Name: usaepay_rep_gtwy_add_cost; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE usaepay_rep_gtwy_add_cost (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.usaepay_rep_gtwy_add_cost OWNER TO axia;

--
-- Name: usaepay_rep_gtwy_cost; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE usaepay_rep_gtwy_cost (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.usaepay_rep_gtwy_cost OWNER TO axia;

--
-- Name: user; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE "user" (
    user_id integer NOT NULL,
    user_type character varying(10) NOT NULL,
    user_title character varying(20),
    user_first_name character varying(50),
    user_last_name character varying(50),
    username character varying(50),
    password character varying(50),
    user_email character varying(50),
    user_phone character varying(20),
    user_fax character varying(20),
    user_admin character varying(1),
    parent_user integer,
    user_commission integer,
    inactive_date date,
    last_login_date date,
    last_login_ip character varying(12),
    entity character varying(10),
    active integer,
    initials character varying(5),
    manager_percentage double precision,
    date_started date,
    split_commissions boolean DEFAULT false NOT NULL,
    bet_extra_pct boolean DEFAULT false NOT NULL,
    parent_user_secondary integer,
    manager_percentage_secondary double precision,
    discover_bet_extra_pct boolean DEFAULT false NOT NULL
);


ALTER TABLE public."user" OWNER TO axia;

--
-- Name: user_bet_table; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE user_bet_table (
    bet_code character varying(50) NOT NULL,
    user_id integer NOT NULL,
    network character varying(20) NOT NULL,
    rate real,
    pi real
);


ALTER TABLE public.user_bet_table OWNER TO axia;

--
-- Name: user_permission; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE user_permission (
    user_id integer NOT NULL,
    permission_id integer NOT NULL
);


ALTER TABLE public.user_permission OWNER TO axia;

--
-- Name: user_type; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE user_type (
    user_type character varying(10) NOT NULL,
    user_type_description character varying(50)
);


ALTER TABLE public.user_type OWNER TO axia;

--
-- Name: uw_approvalinfo; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_approvalinfo (
    id integer NOT NULL,
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    verified_type uw_verified_type NOT NULL
);


ALTER TABLE public.uw_approvalinfo OWNER TO axia;

--
-- Name: uw_approvalinfo_merchant_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_approvalinfo_merchant_xref (
    merchant_id character varying(255) NOT NULL,
    approvalinfo_id integer NOT NULL,
    verified_option_id integer NOT NULL,
    notes text
);


ALTER TABLE public.uw_approvalinfo_merchant_xref OWNER TO axia;

--
-- Name: uw_infodoc; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_infodoc (
    id integer NOT NULL,
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    required boolean NOT NULL
);


ALTER TABLE public.uw_infodoc OWNER TO axia;

--
-- Name: uw_infodoc_merchant_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_infodoc_merchant_xref (
    merchant_id character varying(255) NOT NULL,
    infodoc_id integer NOT NULL,
    received_id integer NOT NULL,
    notes text
);


ALTER TABLE public.uw_infodoc_merchant_xref OWNER TO axia;

--
-- Name: uw_received; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_received (
    id integer NOT NULL,
    name character varying(11) NOT NULL
);


ALTER TABLE public.uw_received OWNER TO axia;

--
-- Name: uw_status; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_status (
    id integer NOT NULL,
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.uw_status OWNER TO axia;

--
-- Name: uw_status_merchant_xref; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_status_merchant_xref (
    merchant_id character varying(255) NOT NULL,
    status_id integer NOT NULL,
    datetime timestamp without time zone NOT NULL,
    notes text
);


ALTER TABLE public.uw_status_merchant_xref OWNER TO axia;

--
-- Name: uw_verified_option; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_verified_option (
    id integer NOT NULL,
    name character varying(37) NOT NULL,
    verified_type uw_verified_type NOT NULL
);


ALTER TABLE public.uw_verified_option OWNER TO axia;

--
-- Name: vendor; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE vendor (
    vendor_id integer NOT NULL,
    vendor_description character varying(50) NOT NULL,
    rank integer
);


ALTER TABLE public.vendor OWNER TO axia;

--
-- Name: virtual_check; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE virtual_check (
    merchant_id character varying(50) NOT NULL,
    vc_mid character varying(20),
    vc_web_based_rate real,
    vc_web_based_pi real,
    vc_monthly_fee real,
    vc_gateway_fee real
);


ALTER TABLE public.virtual_check OWNER TO axia;

--
-- Name: virtual_check_web; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE virtual_check_web (
    merchant_id character varying(50) NOT NULL,
    vcweb_mid character varying(20),
    vcweb_web_based_rate real,
    vcweb_web_based_pi real,
    vcweb_monthly_fee real,
    vcweb_gateway_fee real
);


ALTER TABLE public.virtual_check_web OWNER TO axia;

--
-- Name: warranty; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE warranty (
    warranty integer NOT NULL,
    warranty_description character varying(50) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.warranty OWNER TO axia;

--
-- Name: webpass; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE webpass (
    merchant_id character varying(50) NOT NULL,
    wp_rate real,
    wp_per_item real,
    wp_statement real
);


ALTER TABLE public.webpass OWNER TO axia;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_apips ALTER COLUMN id SET DEFAULT nextval('onlineapp_apips_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobranded_application_aches ALTER COLUMN id SET DEFAULT nextval('onlineapp_cobranded_application_aches_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobranded_application_values ALTER COLUMN id SET DEFAULT nextval('onlineapp_cobranded_application_values_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobranded_applications ALTER COLUMN id SET DEFAULT nextval('onlineapp_cobranded_applications_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobrands ALTER COLUMN id SET DEFAULT nextval('onlineapp_cobrands_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_coversheets ALTER COLUMN id SET DEFAULT nextval('onlineapp_coversheets_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_email_timeline_subjects ALTER COLUMN id SET DEFAULT nextval('onlineapp_email_timeline_subjects_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_email_timelines ALTER COLUMN id SET DEFAULT nextval('onlineapp_email_timelines_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_epayments ALTER COLUMN id SET DEFAULT nextval('onlineapp_epayments_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_groups ALTER COLUMN id SET DEFAULT nextval('onlineapp_groups_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_template_fields ALTER COLUMN id SET DEFAULT nextval('onlineapp_template_fields_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_template_pages ALTER COLUMN id SET DEFAULT nextval('onlineapp_template_pages_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_template_sections ALTER COLUMN id SET DEFAULT nextval('onlineapp_template_sections_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_templates ALTER COLUMN id SET DEFAULT nextval('onlineapp_templates_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_users ALTER COLUMN id SET DEFAULT nextval('onlineapp_users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_billing ALTER COLUMN id SET DEFAULT nextval('pci_billing_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_billing_type ALTER COLUMN id SET DEFAULT nextval('pci_billing_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_compliance ALTER COLUMN id SET DEFAULT nextval('pci_compliance_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_compliance_date_type ALTER COLUMN id SET DEFAULT nextval('pci_compliance_date_type_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_control_scan_unboarded ALTER COLUMN id SET DEFAULT nextval('saq_control_scan_unboarded_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY schema_migrations ALTER COLUMN id SET DEFAULT nextval('schema_migrations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY tickler_loggable_logs ALTER COLUMN id SET DEFAULT nextval('tickler_loggable_logs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY tickler_schema_migrations ALTER COLUMN id SET DEFAULT nextval('tickler_schema_migrations_id_seq'::regclass);


--
-- Name: ach_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY ach
    ADD CONSTRAINT ach_pk PRIMARY KEY (merchant_id);


--
-- Name: address_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY address
    ADD CONSTRAINT address_pk PRIMARY KEY (address_id);


--
-- Name: address_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY address_type
    ADD CONSTRAINT address_type_pk PRIMARY KEY (address_type);


--
-- Name: adjustments_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY adjustments
    ADD CONSTRAINT adjustments_pk PRIMARY KEY (adj_seq_number);


--
-- Name: adjustments_t_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY adjustments_t
    ADD CONSTRAINT adjustments_t_pk PRIMARY KEY (adj_seq_number);


--
-- Name: admin_entity_view_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY admin_entity_view
    ADD CONSTRAINT admin_entity_view_pk PRIMARY KEY (user_id, entity);


--
-- Name: amex_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY amex
    ADD CONSTRAINT amex_pkey PRIMARY KEY (merchant_id);


--
-- Name: bankcard_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY bankcard
    ADD CONSTRAINT bankcard_pk PRIMARY KEY (merchant_id);


--
-- Name: bet_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY bet
    ADD CONSTRAINT bet_pk PRIMARY KEY (bet_code);


--
-- Name: cancellation_fee_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY cancellation_fee
    ADD CONSTRAINT cancellation_fee_pkey PRIMARY KEY (cancellation_fee_id);


--
-- Name: card_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY card_type
    ADD CONSTRAINT card_type_pk PRIMARY KEY (card_type);


--
-- Name: change_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY change_type
    ADD CONSTRAINT change_type_pk PRIMARY KEY (change_type);


--
-- Name: check_guarantee_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY check_guarantee
    ADD CONSTRAINT check_guarantee_pk PRIMARY KEY (merchant_id);


--
-- Name: debit_acquirer_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY debit_acquirer
    ADD CONSTRAINT debit_acquirer_pkey PRIMARY KEY (debit_acquirer_id);


--
-- Name: debit_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY debit
    ADD CONSTRAINT debit_pk PRIMARY KEY (merchant_id);


--
-- Name: discover_bet_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY discover_bet
    ADD CONSTRAINT discover_bet_pkey PRIMARY KEY (bet_code);


--
-- Name: discover_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY discover
    ADD CONSTRAINT discover_pkey PRIMARY KEY (merchant_id);


--
-- Name: discover_user_bet_table_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY discover_user_bet_table
    ADD CONSTRAINT discover_user_bet_table_pkey PRIMARY KEY (bet_code, user_id, network);


--
-- Name: ebt_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY ebt
    ADD CONSTRAINT ebt_pk PRIMARY KEY (merchant_id);


--
-- Name: entity_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY entity
    ADD CONSTRAINT entity_pk PRIMARY KEY (entity);


--
-- Name: eptx_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY equipment_programming_type_xref
    ADD CONSTRAINT eptx_pk PRIMARY KEY (programming_id, programming_type);


--
-- Name: equipment_item_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY equipment_item
    ADD CONSTRAINT equipment_item_pk PRIMARY KEY (equipment_item);


--
-- Name: equipment_programming_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY equipment_programming
    ADD CONSTRAINT equipment_programming_pk PRIMARY KEY (programming_id);


--
-- Name: equipment_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY equipment_type
    ADD CONSTRAINT equipment_type_pk PRIMARY KEY (equipment_type);


--
-- Name: gateway1_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY gateway1
    ADD CONSTRAINT gateway1_pkey PRIMARY KEY (merchant_id);


--
-- Name: gateway2_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY gateway2
    ADD CONSTRAINT gateway2_pkey PRIMARY KEY (merchant_id);


--
-- Name: gateway_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY gateway
    ADD CONSTRAINT gateway_pkey PRIMARY KEY (merchant_id);


--
-- Name: gateways_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY gateways
    ADD CONSTRAINT gateways_pkey PRIMARY KEY (id);


--
-- Name: gift_card_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY gift_card
    ADD CONSTRAINT gift_card_pk PRIMARY KEY (merchant_id);


--
-- Name: group_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY "group"
    ADD CONSTRAINT group_pk PRIMARY KEY (group_id);


--
-- Name: merchant_ach_app_status_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_ach_app_status
    ADD CONSTRAINT merchant_ach_app_status_pkey PRIMARY KEY (app_status_id);


--
-- Name: merchant_ach_billing_optio_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_ach_billing_option
    ADD CONSTRAINT merchant_ach_billing_optio_pkey PRIMARY KEY (billing_option_id);


--
-- Name: merchant_ach_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_ach
    ADD CONSTRAINT merchant_ach_pk PRIMARY KEY (ach_seq_number);


--
-- Name: merchant_acquirer_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_acquirer
    ADD CONSTRAINT merchant_acquirer_pkey PRIMARY KEY (acquirer_id);


--
-- Name: merchant_bank_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_bank
    ADD CONSTRAINT merchant_bank_pk PRIMARY KEY (merchant_id);


--
-- Name: merchant_bin_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_bin
    ADD CONSTRAINT merchant_bin_pkey PRIMARY KEY (bin_id);


--
-- Name: merchant_cancellation_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_cancellation
    ADD CONSTRAINT merchant_cancellation_pkey PRIMARY KEY (merchant_id);


--
-- Name: merchant_cancellation_subr_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_cancellation_subreason
    ADD CONSTRAINT merchant_cancellation_subr_pkey PRIMARY KEY (id);


--
-- Name: merchant_card_types_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_card_types
    ADD CONSTRAINT merchant_card_types_pk PRIMARY KEY (merchant_id, card_type);


--
-- Name: merchant_change_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_change
    ADD CONSTRAINT merchant_change_pk PRIMARY KEY (change_id);


--
-- Name: merchant_note_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_note
    ADD CONSTRAINT merchant_note_pk PRIMARY KEY (merchant_note_id);


--
-- Name: merchant_owner_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_owner
    ADD CONSTRAINT merchant_owner_pk PRIMARY KEY (owner_id);


--
-- Name: merchant_pci_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_pci
    ADD CONSTRAINT merchant_pci_pk PRIMARY KEY (merchant_id);


--
-- Name: merchant_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT merchant_pk PRIMARY KEY (merchant_id);


--
-- Name: merchant_reference_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_reference
    ADD CONSTRAINT merchant_reference_pkey PRIMARY KEY (merchant_ref_seq_number);


--
-- Name: merchant_reject_line_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_reject_line
    ADD CONSTRAINT merchant_reject_line_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_reject
    ADD CONSTRAINT merchant_reject_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_recurrance_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_reject_recurrance
    ADD CONSTRAINT merchant_reject_recurrance_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_status_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_reject_status
    ADD CONSTRAINT merchant_reject_status_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_type_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_reject_type
    ADD CONSTRAINT merchant_reject_type_pkey PRIMARY KEY (id);


--
-- Name: merchant_uw_final_approved_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_uw_final_approved
    ADD CONSTRAINT merchant_uw_final_approved_pkey PRIMARY KEY (id);


--
-- Name: merchant_uw_final_status_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_uw_final_status
    ADD CONSTRAINT merchant_uw_final_status_pkey PRIMARY KEY (id);


--
-- Name: merchant_uw_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant_uw
    ADD CONSTRAINT merchant_uw_pkey PRIMARY KEY (merchant_id);


--
-- Name: network_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY network
    ADD CONSTRAINT network_pk PRIMARY KEY (network_id);


--
-- Name: note_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY note_type
    ADD CONSTRAINT note_type_pk PRIMARY KEY (note_type);


--
-- Name: oaid_unique; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT oaid_unique UNIQUE (onlineapp_application_id);


--
-- Name: onlineapp_api_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_api_logs
    ADD CONSTRAINT onlineapp_api_logs_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_apips_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_apips
    ADD CONSTRAINT onlineapp_apips_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_application_id_key; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_coversheets
    ADD CONSTRAINT onlineapp_application_id_key UNIQUE (onlineapp_application_id);


--
-- Name: onlineapp_applications_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_applications
    ADD CONSTRAINT onlineapp_applications_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobranded_application_aches_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_cobranded_application_aches
    ADD CONSTRAINT onlineapp_cobranded_application_aches_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobranded_application_values_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_cobranded_application_values
    ADD CONSTRAINT onlineapp_cobranded_application_values_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobranded_applications_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_cobranded_applications
    ADD CONSTRAINT onlineapp_cobranded_applications_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobrands_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_cobrands
    ADD CONSTRAINT onlineapp_cobrands_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_coversheets_cobranded_application_id_key; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_coversheets
    ADD CONSTRAINT onlineapp_coversheets_cobranded_application_id_key UNIQUE (cobranded_application_id);


--
-- Name: onlineapp_coversheets_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_coversheets
    ADD CONSTRAINT onlineapp_coversheets_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_email_timeline_subjects_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_email_timeline_subjects
    ADD CONSTRAINT onlineapp_email_timeline_subjects_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_email_timelines_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_email_timelines
    ADD CONSTRAINT onlineapp_email_timelines_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_epayments_merchant_id_key; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_merchant_id_key UNIQUE (merchant_id);


--
-- Name: onlineapp_epayments_onlineapp_applications_id_key; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_onlineapp_applications_id_key UNIQUE (onlineapp_application_id);


--
-- Name: onlineapp_epayments_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_groups
    ADD CONSTRAINT onlineapp_groups_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_multipasses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_multipasses
    ADD CONSTRAINT onlineapp_multipasses_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_settings
    ADD CONSTRAINT onlineapp_settings_pkey PRIMARY KEY (key);


--
-- Name: onlineapp_template_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_template_fields
    ADD CONSTRAINT onlineapp_template_fields_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_template_pages_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_template_pages
    ADD CONSTRAINT onlineapp_template_pages_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_template_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_template_sections
    ADD CONSTRAINT onlineapp_template_sections_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_templates
    ADD CONSTRAINT onlineapp_templates_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_users_email_key; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_users
    ADD CONSTRAINT onlineapp_users_email_key UNIQUE (email);


--
-- Name: onlineapp_users_managers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_users_managers
    ADD CONSTRAINT onlineapp_users_managers_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_users_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_users
    ADD CONSTRAINT onlineapp_users_pkey PRIMARY KEY (id);


--
-- Name: order-tems_replacement_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY orderitems_replacement
    ADD CONSTRAINT "order-tems_replacement_pkey" PRIMARY KEY (orderitem_replacement_id);


--
-- Name: orderitem_type_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY orderitem_type
    ADD CONSTRAINT orderitem_type_pkey PRIMARY KEY (orderitem_type_id);


--
-- Name: orderitems_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_pk PRIMARY KEY (orderitem_id);


--
-- Name: orders_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT orders_pk PRIMARY KEY (order_id);


--
-- Name: partner_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY partner
    ADD CONSTRAINT partner_pkey PRIMARY KEY (partner_id);


--
-- Name: pci_billing_type_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY pci_billing_type
    ADD CONSTRAINT pci_billing_type_pkey PRIMARY KEY (id);


--
-- Name: pci_compliance_date_type_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY pci_compliance_date_type
    ADD CONSTRAINT pci_compliance_date_type_pkey PRIMARY KEY (id);


--
-- Name: permission_group_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY permission_group
    ADD CONSTRAINT permission_group_pk PRIMARY KEY (permission_group);


--
-- Name: permission_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY permission
    ADD CONSTRAINT permission_pk PRIMARY KEY (permission_id);


--
-- Name: pkey_saq_answer; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_answer
    ADD CONSTRAINT pkey_saq_answer PRIMARY KEY (id);


--
-- Name: pkey_saq_merchant; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_merchant
    ADD CONSTRAINT pkey_saq_merchant PRIMARY KEY (id);


--
-- Name: pkey_saq_merchant_survey_xref; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_merchant_survey_xref
    ADD CONSTRAINT pkey_saq_merchant_survey_xref PRIMARY KEY (id);


--
-- Name: pkey_saq_prequalification; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_prequalification
    ADD CONSTRAINT pkey_saq_prequalification PRIMARY KEY (id);


--
-- Name: pkey_saq_question; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_question
    ADD CONSTRAINT pkey_saq_question PRIMARY KEY (id);


--
-- Name: pkey_saq_survey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_survey
    ADD CONSTRAINT pkey_saq_survey PRIMARY KEY (id);


--
-- Name: pkey_saq_survey_question_xref; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_survey_question_xref
    ADD CONSTRAINT pkey_saq_survey_question_xref PRIMARY KEY (id);


--
-- Name: pricing_matrix_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY pricing_matrix
    ADD CONSTRAINT pricing_matrix_pk PRIMARY KEY (matrix_id);


--
-- Name: products_and_services_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY products_and_services
    ADD CONSTRAINT products_and_services_pk PRIMARY KEY (merchant_id, products_services_type);


--
-- Name: products_services_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY products_services_type
    ADD CONSTRAINT products_services_type_pk PRIMARY KEY (products_services_type);


--
-- Name: referer_products_services_xref_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY referer_products_services_xref
    ADD CONSTRAINT referer_products_services_xref_pkey PRIMARY KEY (ref_seq_number, products_services_type);


--
-- Name: referers_bet_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY referers_bet
    ADD CONSTRAINT referers_bet_pkey PRIMARY KEY (referers_bet_id);


--
-- Name: referers_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY referers
    ADD CONSTRAINT referers_pk PRIMARY KEY (ref_seq_number);


--
-- Name: rep_cost_structure_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY rep_cost_structure
    ADD CONSTRAINT rep_cost_structure_pk PRIMARY KEY (user_id);


--
-- Name: rep_partner_xref_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY rep_partner_xref
    ADD CONSTRAINT rep_partner_xref_pkey PRIMARY KEY (user_id, partner_id);


--
-- Name: rep_product_profit_pct_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY rep_product_profit_pct
    ADD CONSTRAINT rep_product_profit_pct_pkey PRIMARY KEY (user_id, products_services_type);


--
-- Name: saq_control_scan_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_control_scan
    ADD CONSTRAINT saq_control_scan_pkey PRIMARY KEY (merchant_id);


--
-- Name: saq_control_scan_unboarded_merchant_id_key; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_control_scan_unboarded
    ADD CONSTRAINT saq_control_scan_unboarded_merchant_id_key UNIQUE (merchant_id);


--
-- Name: saq_merchant_pci_email_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_merchant_pci_email
    ADD CONSTRAINT saq_merchant_pci_email_pkey PRIMARY KEY (id);


--
-- Name: saq_merchant_pci_email_sent_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY saq_merchant_pci_email_sent
    ADD CONSTRAINT saq_merchant_pci_email_sent_pkey PRIMARY KEY (id);


--
-- Name: schema_migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY schema_migrations
    ADD CONSTRAINT schema_migrations_pkey PRIMARY KEY (id);


--
-- Name: shipping_type_item_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY shipping_type_item
    ADD CONSTRAINT shipping_type_item_pkey PRIMARY KEY (shipping_type);


--
-- Name: system_transaction_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY system_transaction
    ADD CONSTRAINT system_transaction_pk PRIMARY KEY (system_transaction_id);


--
-- Name: tgate_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tgate
    ADD CONSTRAINT tgate_pkey PRIMARY KEY (merchant_id);


--
-- Name: tickler_availabilities_leads_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_availabilities_leads
    ADD CONSTRAINT tickler_availabilities_leads_pkey PRIMARY KEY (id);


--
-- Name: tickler_availabilities_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_availabilities
    ADD CONSTRAINT tickler_availabilities_pkey PRIMARY KEY (id);


--
-- Name: tickler_comments_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_comments
    ADD CONSTRAINT tickler_comments_pkey PRIMARY KEY (id);


--
-- Name: tickler_companies_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_companies
    ADD CONSTRAINT tickler_companies_pkey PRIMARY KEY (id);


--
-- Name: tickler_equipments_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_equipments
    ADD CONSTRAINT tickler_equipments_pkey PRIMARY KEY (id);


--
-- Name: tickler_followups_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_followups
    ADD CONSTRAINT tickler_followups_pkey PRIMARY KEY (id);


--
-- Name: tickler_leads_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_leads_logs
    ADD CONSTRAINT tickler_leads_logs_pkey PRIMARY KEY (id);


--
-- Name: tickler_leads_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_leads
    ADD CONSTRAINT tickler_leads_pkey PRIMARY KEY (id);


--
-- Name: tickler_loggable_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_loggable_logs
    ADD CONSTRAINT tickler_loggable_logs_pkey PRIMARY KEY (id);


--
-- Name: tickler_referers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_referers
    ADD CONSTRAINT tickler_referers_pkey PRIMARY KEY (id);


--
-- Name: tickler_resellers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_resellers
    ADD CONSTRAINT tickler_resellers_pkey PRIMARY KEY (id);


--
-- Name: tickler_schema_migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_schema_migrations
    ADD CONSTRAINT tickler_schema_migrations_pkey PRIMARY KEY (id);


--
-- Name: tickler_states_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_states
    ADD CONSTRAINT tickler_states_pkey PRIMARY KEY (id);


--
-- Name: tickler_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY tickler_statuses
    ADD CONSTRAINT tickler_statuses_pkey PRIMARY KEY (id);


--
-- Name: timeline_entries_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY timeline_entries
    ADD CONSTRAINT timeline_entries_pk PRIMARY KEY (merchant_id, timeline_item);


--
-- Name: timeline_item_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY timeline_item
    ADD CONSTRAINT timeline_item_pk PRIMARY KEY (timeline_item);


--
-- Name: token; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY onlineapp_users
    ADD CONSTRAINT token UNIQUE (token);


--
-- Name: transaction_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY transaction_type
    ADD CONSTRAINT transaction_type_pk PRIMARY KEY (transaction_type);


--
-- Name: usaepay_rep_gtwy_add_cost_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY usaepay_rep_gtwy_add_cost
    ADD CONSTRAINT usaepay_rep_gtwy_add_cost_pkey PRIMARY KEY (id);


--
-- Name: usaepay_rep_gtwy_cost_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY usaepay_rep_gtwy_cost
    ADD CONSTRAINT usaepay_rep_gtwy_cost_pkey PRIMARY KEY (id);


--
-- Name: user_permission_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY user_permission
    ADD CONSTRAINT user_permission_pk PRIMARY KEY (user_id, permission_id);


--
-- Name: user_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pk PRIMARY KEY (user_id);


--
-- Name: user_type_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY user_type
    ADD CONSTRAINT user_type_pk PRIMARY KEY (user_type);


--
-- Name: uw_approvalinfo_merchant_xref_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_approvalinfo_merchant_xref
    ADD CONSTRAINT uw_approvalinfo_merchant_xref_pkey PRIMARY KEY (merchant_id, approvalinfo_id);


--
-- Name: uw_approvalinfo_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_approvalinfo
    ADD CONSTRAINT uw_approvalinfo_pkey PRIMARY KEY (id);


--
-- Name: uw_infodoc_merchant_xref_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_infodoc_merchant_xref
    ADD CONSTRAINT uw_infodoc_merchant_xref_pkey PRIMARY KEY (merchant_id, infodoc_id);


--
-- Name: uw_infodoc_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_infodoc
    ADD CONSTRAINT uw_infodoc_pkey PRIMARY KEY (id);


--
-- Name: uw_received_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_received
    ADD CONSTRAINT uw_received_pkey PRIMARY KEY (id);


--
-- Name: uw_status_merchant_xref_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_status_merchant_xref
    ADD CONSTRAINT uw_status_merchant_xref_pkey PRIMARY KEY (merchant_id, status_id);


--
-- Name: uw_status_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_status
    ADD CONSTRAINT uw_status_pkey PRIMARY KEY (id);


--
-- Name: uw_verified_option_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY uw_verified_option
    ADD CONSTRAINT uw_verified_option_pkey PRIMARY KEY (id);


--
-- Name: vendor_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_pkey PRIMARY KEY (vendor_id);


--
-- Name: virtual_check_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY virtual_check
    ADD CONSTRAINT virtual_check_pk PRIMARY KEY (merchant_id);


--
-- Name: virtual_check_web_pk; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY virtual_check_web
    ADD CONSTRAINT virtual_check_web_pk PRIMARY KEY (merchant_id);


--
-- Name: warranty_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY warranty
    ADD CONSTRAINT warranty_pkey PRIMARY KEY (warranty);


--
-- Name: webpass_pkey; Type: CONSTRAINT; Schema: public; Owner: axia; Tablespace: 
--

ALTER TABLE ONLY webpass
    ADD CONSTRAINT webpass_pkey PRIMARY KEY (merchant_id);


--
-- Name: ach_merchantid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ach_merchantid ON ach USING btree (merchant_id);


--
-- Name: ach_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ach_mid ON ach USING btree (ach_mid);


--
-- Name: action_taken; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX action_taken ON tickler_leads_logs USING btree (action);


--
-- Name: address_merchant_type_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX address_merchant_type_idx ON address USING btree (address_type, merchant_id);


--
-- Name: address_owner_uidx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX address_owner_uidx ON address USING btree (owner_id);


--
-- Name: adjustments_t_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX adjustments_t_user_idx ON adjustments_t USING btree (user_id);


--
-- Name: adjustments_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX adjustments_user_idx ON adjustments USING btree (user_id);


--
-- Name: admin_entity_view_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX admin_entity_view_user_idx ON admin_entity_view USING btree (user_id);


--
-- Name: api_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX api_idx ON onlineapp_applications USING btree (api);


--
-- Name: application_id_key; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX application_id_key ON onlineapp_multipasses USING btree (application_id);


--
-- Name: approved; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX approved ON tickler_comments USING btree (approved);


--
-- Name: bankcard_bcmid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_bcmid ON bankcard USING btree (bc_mid);


--
-- Name: bankcard_bet; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_bet ON bankcard USING btree (bet_code);


--
-- Name: bankcard_urgac_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_urgac_index ON bankcard USING btree (bc_usaepay_rep_gtwy_add_cost_id);


--
-- Name: bankcard_urgc_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_urgc_index ON bankcard USING btree (bc_usaepay_rep_gtwy_cost_id);


--
-- Name: comment_type; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX comment_type ON tickler_comments USING btree (comment_type);


--
-- Name: commission_pricing_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index1 ON commission_pricing USING btree (merchant_id);


--
-- Name: commission_pricing_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index2 ON commission_pricing USING btree (user_id);


--
-- Name: commission_pricing_index3; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index3 ON commission_pricing USING btree (c_month);


--
-- Name: commission_pricing_index4; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index4 ON commission_pricing USING btree (c_year);


--
-- Name: commission_pricing_products_services_type; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_products_services_type ON commission_pricing USING btree (products_services_type);


--
-- Name: corp_contact_name_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX corp_contact_name_idx ON onlineapp_applications USING btree (corp_contact_name);


--
-- Name: cr_mid_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_mid_idx ON commission_report_old USING btree (merchant_id);


--
-- Name: cr_month_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_month_index ON commission_report USING btree (c_month);


--
-- Name: cr_partner_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_partner_id_index ON commission_report USING btree (partner_id);


--
-- Name: cr_status_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_status_index ON commission_report USING btree (status);


--
-- Name: cr_userid_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_userid_index ON commission_report USING btree (user_id);


--
-- Name: cr_year_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_year_index ON commission_report USING btree (c_year);


--
-- Name: created; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX created ON tickler_comments USING btree (created);


--
-- Name: crnew_mid_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX crnew_mid_idx ON commission_report USING btree (merchant_id);


--
-- Name: currentcompany; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX currentcompany ON tickler_leads USING btree (company_id);


--
-- Name: dba_business_name_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX dba_business_name_idx ON onlineapp_applications USING btree (dba_business_name);


--
-- Name: device_number_key; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX device_number_key ON onlineapp_multipasses USING btree (device_number);


--
-- Name: discover_bet_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX discover_bet_index ON discover USING btree (bet_code);


--
-- Name: discover_user_bet_table_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX discover_user_bet_table_index1 ON discover_user_bet_table USING btree (user_id);


--
-- Name: eptx_programming_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX eptx_programming_idx ON equipment_programming_type_xref USING btree (programming_id);


--
-- Name: equipment_item_active; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_item_active ON equipment_item USING btree (active);


--
-- Name: equipment_item_type; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_item_type ON equipment_item USING btree (equipment_type);


--
-- Name: equipment_item_warranty; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_item_warranty ON equipment_item USING btree (warranty);


--
-- Name: equipment_programming_appid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_appid ON equipment_programming USING btree (app_id);


--
-- Name: equipment_programming_merch_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_merch_idx ON equipment_programming USING btree (merchant_id);


--
-- Name: equipment_programming_serialnum; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_serialnum ON equipment_programming USING btree (serial_number);


--
-- Name: equipment_programming_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_status ON equipment_programming USING btree (status);


--
-- Name: equipment_programming_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_userid ON equipment_programming USING btree (user_id);


--
-- Name: equipmentlead; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipmentlead ON tickler_leads USING btree (equipment_id);


--
-- Name: foreign_key; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX foreign_key ON tickler_comments USING btree (foreign_key);


--
-- Name: gateway_gw_usaepay_add_cost; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gateway_gw_usaepay_add_cost ON gateway USING btree (gw_usaepay_rep_gtwy_add_cost_id);


--
-- Name: gateway_gw_usaepay_cost; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gateway_gw_usaepay_cost ON gateway USING btree (gw_usaepay_rep_gtwy_cost_id);


--
-- Name: gw1_gatewayid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gw1_gatewayid ON gateway1 USING btree (gw1_gateway_id);


--
-- Name: gw2_gatewayid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gw2_gatewayid ON gateway2 USING btree (gw2_gateway_id);


--
-- Name: hash_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX hash_idx ON onlineapp_applications USING btree (hash);


--
-- Name: install_var_rs_document_guid_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX install_var_rs_document_guid_idx ON onlineapp_applications USING btree (install_var_rs_document_guid);


--
-- Name: is_spam; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX is_spam ON tickler_comments USING btree (is_spam);


--
-- Name: language; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX language ON tickler_comments USING btree (language);


--
-- Name: lead; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX lead ON tickler_leads_logs USING btree (lead_id);


--
-- Name: legal_business_name_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX legal_business_name_idx ON onlineapp_applications USING btree (legal_business_name);


--
-- Name: lft; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX lft ON tickler_comments USING btree (lft);


--
-- Name: logged; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX logged ON tickler_leads_logs USING btree (created);


--
-- Name: mailing_city_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mailing_city_idx ON onlineapp_applications USING btree (mailing_city);


--
-- Name: mct_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mct_merchant_idx ON merchant_card_types USING btree (merchant_id);


--
-- Name: merchant_ach_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_ach_idx ON merchant_ach USING btree (merchant_id);


--
-- Name: merchant_active; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_active ON merchant USING btree (active);


--
-- Name: merchant_cancellation_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index1 ON merchant_cancellation USING btree (date_submitted);


--
-- Name: merchant_cancellation_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index2 ON merchant_cancellation USING btree (date_completed);


--
-- Name: merchant_cancellation_index3; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index3 ON merchant_cancellation USING btree (status);


--
-- Name: merchant_cancellation_index9; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index9 ON merchant_cancellation USING btree (date_inactive);


--
-- Name: merchant_cancellation_index99; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index99 ON merchant_cancellation USING btree (subreason_id);


--
-- Name: merchant_change_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_change_merchant_idx ON merchant_change USING btree (merchant_id);


--
-- Name: merchant_dba_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_dba_idx ON merchant USING btree (merchant_dba);


--
-- Name: merchant_dbalower_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_dbalower_idx ON merchant USING btree (merchant_dba);


--
-- Name: merchant_entity; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_entity ON merchant USING btree (entity);


--
-- Name: merchant_groupid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_groupid ON merchant USING btree (group_id);


--
-- Name: merchant_id_key; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX merchant_id_key ON onlineapp_multipasses USING btree (merchant_id);


--
-- Name: merchant_netid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_netid ON merchant USING btree (network_id);


--
-- Name: merchant_note_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_note_merchant_idx ON merchant_note USING btree (merchant_id);


--
-- Name: merchant_owner_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_owner_merchant_idx ON merchant_owner USING btree (merchant_id);


--
-- Name: merchant_reference_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_reference_mid ON merchant_reference USING btree (merchant_id);


--
-- Name: merchant_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_userid ON merchant USING btree (user_id);


--
-- Name: merchant_uw_expedited_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_expedited_index ON merchant_uw USING btree (expedited);


--
-- Name: merchant_uw_final_approved_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_final_approved_id_index ON merchant_uw USING btree (final_approved_id);


--
-- Name: merchant_uw_final_date_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_final_date_index ON merchant_uw USING btree (final_date);


--
-- Name: merchant_uw_final_status_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_final_status_id_index ON merchant_uw USING btree (final_status_id);


--
-- Name: merchant_uw_tier_assignment_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_tier_assignment_index ON merchant_uw USING btree (tier_assignment);


--
-- Name: modified_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX modified_idx ON onlineapp_applications USING btree (modified);


--
-- Name: mp_compliance_fee; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mp_compliance_fee ON merchant_pci USING btree (compliance_fee);


--
-- Name: mp_insurance_fee; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mp_insurance_fee ON merchant_pci USING btree (insurance_fee);


--
-- Name: mp_saq_completed_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mp_saq_completed_date ON merchant_pci USING btree (saq_completed_date);


--
-- Name: mpid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mpid_index1 ON merchant USING btree (partner_id);


--
-- Name: mr_merchant_id_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_merchant_id_index1 ON merchant_reject USING btree (merchant_id);


--
-- Name: mr_open_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_open_index1 ON merchant_reject USING btree (open);


--
-- Name: mr_recurranceid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_recurranceid_index1 ON merchant_reject USING btree (recurranceid);


--
-- Name: mr_reject_date_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_reject_date_index1 ON merchant_reject USING btree (reject_date);


--
-- Name: mr_status_date_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_status_date_index1 ON merchant_reject_line USING btree (status_date);


--
-- Name: mr_trace_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX mr_trace_index1 ON merchant_reject USING btree (trace);


--
-- Name: mr_typeid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_typeid_index1 ON merchant_reject USING btree (typeid);


--
-- Name: mrl_rejectid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mrl_rejectid_index1 ON merchant_reject_line USING btree (rejectid);


--
-- Name: mrl_statusid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mrl_statusid_index1 ON merchant_reject_line USING btree (statusid);


--
-- Name: mrs_collected_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mrs_collected_index1 ON merchant_reject_status USING btree (collected);


--
-- Name: onlineapp_api_logs_user_id_key; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX onlineapp_api_logs_user_id_key ON onlineapp_api_logs USING btree (user_id);


--
-- Name: orderitems_eqtype; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orderitems_eqtype ON orderitems USING btree (equipment_type);


--
-- Name: orderitems_itemmerchid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orderitems_itemmerchid ON orderitems USING btree (item_merchant_id);


--
-- Name: orderitems_order_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orderitems_order_idx ON orderitems USING btree (order_id);


--
-- Name: orderitems_replacement_oid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orderitems_replacement_oid ON orderitems_replacement USING btree (orderitem_id);


--
-- Name: orderitems_typeid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orderitems_typeid ON orderitems USING btree (type_id);


--
-- Name: orders_commissionmonth; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orders_commissionmonth ON orders USING btree (commission_month);


--
-- Name: orders_invnum; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orders_invnum ON orders USING btree (invoice_number);


--
-- Name: orders_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orders_merchant_idx ON orders USING btree (merchant_id);


--
-- Name: orders_shiptype; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orders_shiptype ON orders USING btree (shipping_type);


--
-- Name: orders_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orders_status ON orders USING btree (status);


--
-- Name: orders_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orders_user_idx ON orders USING btree (user_id);


--
-- Name: orders_vendorid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX orders_vendorid ON orders USING btree (vendor_id);


--
-- Name: owner1_fullname_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX owner1_fullname_idx ON onlineapp_applications USING btree (owner1_fullname);


--
-- Name: owner2_fullname_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX owner2_fullname_idx ON onlineapp_applications USING btree (owner2_fullname);


--
-- Name: p_and_s_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX p_and_s_merchant_idx ON products_and_services USING btree (merchant_id);


--
-- Name: pactive_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pactive_index1 ON partner USING btree (active);


--
-- Name: parent_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX parent_id ON tickler_comments USING btree (parent_id);


--
-- Name: pricing_matrix_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pricing_matrix_user_idx ON pricing_matrix USING btree (user_id);


--
-- Name: pricing_matrix_usertype; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pricing_matrix_usertype ON pricing_matrix USING btree (user_type);


--
-- Name: pst_rppp_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pst_rppp_index1 ON products_services_type USING btree (products_services_rppp);


--
-- Name: pst_rppp_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pst_rppp_index2 ON rep_product_profit_pct USING btree (do_not_display);


--
-- Name: refererlead; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX refererlead ON tickler_leads USING btree (referer_id);


--
-- Name: referers_active; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX referers_active ON referers USING btree (active);


--
-- Name: referers_bet_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX referers_bet_index1 ON referers_bet USING btree (ref_seq_number);


--
-- Name: referers_bet_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX referers_bet_index2 ON referers_bet USING btree (bet_code);


--
-- Name: representative; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX representative ON tickler_leads USING btree (user_id);


--
-- Name: resellerlead; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX resellerlead ON tickler_leads USING btree (reseller_id);


--
-- Name: residual_pricing_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_mid ON residual_pricing USING btree (merchant_id);


--
-- Name: residual_pricing_month; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_month ON residual_pricing USING btree (r_month);


--
-- Name: residual_pricing_network; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_network ON residual_pricing USING btree (r_network);


--
-- Name: residual_pricing_pst; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_pst ON residual_pricing USING btree (products_services_type);


--
-- Name: residual_pricing_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_userid ON residual_pricing USING btree (user_id);


--
-- Name: residual_pricing_year; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_year ON residual_pricing USING btree (r_year);


--
-- Name: residual_report_merchantid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_merchantid ON residual_report USING btree (merchant_id);


--
-- Name: residual_report_month; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_month ON residual_report USING btree (r_month);


--
-- Name: residual_report_pst; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_pst ON residual_report USING btree (products_services_type);


--
-- Name: residual_report_seqnum; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_seqnum ON residual_report USING btree (ref_seq_number);


--
-- Name: residual_report_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_status ON residual_report USING btree (status);


--
-- Name: residual_report_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_userid ON residual_report USING btree (user_id);


--
-- Name: residual_report_year; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_year ON residual_report USING btree (r_year);


--
-- Name: rght; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rght ON tickler_comments USING btree (rght);


--
-- Name: rpxref_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rpxref_index1 ON rep_partner_xref USING btree (mgr1_id);


--
-- Name: rpxref_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rpxref_index2 ON rep_partner_xref USING btree (mgr2_id);


--
-- Name: rr_manager_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rr_manager_id ON residual_report USING btree (manager_id);


--
-- Name: rr_manager_id_secondary; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rr_manager_id_secondary ON residual_report USING btree (manager_id_secondary);


--
-- Name: rr_partner_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rr_partner_id_index ON residual_report USING btree (partner_id);


--
-- Name: rs_document_guid_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rs_document_guid_idx ON onlineapp_applications USING btree (rs_document_guid);


--
-- Name: sa_saq_merchant_survey_xref_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sa_saq_merchant_survey_xref_id ON saq_answer USING btree (saq_merchant_survey_xref_id);


--
-- Name: sa_saq_survey_question_xref_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sa_saq_survey_question_xref_id ON saq_answer USING btree (saq_survey_question_xref_id);


--
-- Name: sales_goal_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sales_goal_userid ON sales_goal USING btree (user_id);


--
-- Name: saw_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX saw_merchant_idx ON saq_merchant USING btree (merchant_id);


--
-- Name: scs_creation_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_creation_date ON saq_control_scan USING btree (creation_date);


--
-- Name: scs_first_questionnaire_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_first_questionnaire_date ON saq_control_scan USING btree (first_questionnaire_date);


--
-- Name: scs_first_scan_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_first_scan_date ON saq_control_scan USING btree (first_scan_date);


--
-- Name: scs_pci_compliance; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_pci_compliance ON saq_control_scan USING btree (pci_compliance);


--
-- Name: scs_quarterly_scan_fee; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_quarterly_scan_fee ON saq_control_scan USING btree (quarterly_scan_fee);


--
-- Name: scs_questionnaire_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_questionnaire_status ON saq_control_scan USING btree (questionnaire_status);


--
-- Name: scs_saq_type; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_saq_type ON saq_control_scan USING btree (saq_type);


--
-- Name: scs_scan_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_scan_status ON saq_control_scan USING btree (scan_status);


--
-- Name: scs_sua; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_sua ON saq_control_scan USING btree (sua);


--
-- Name: scsu_date_unboarded; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scsu_date_unboarded ON saq_control_scan_unboarded USING btree (date_unboarded);


--
-- Name: sg_archive_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX sg_archive_user_idx ON sales_goal_archive USING btree (user_id, goal_month, goal_year);


--
-- Name: sm_billing_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_billing_date ON saq_merchant USING btree (billing_date);


--
-- Name: sm_email_sent; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_email_sent ON saq_merchant USING btree (email_sent);


--
-- Name: sm_merchant_email; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_merchant_email ON saq_merchant USING btree (merchant_email);


--
-- Name: sm_merchant_name; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_merchant_name ON saq_merchant USING btree (merchant_name);


--
-- Name: sm_next_billing_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_next_billing_date ON saq_merchant USING btree (next_billing_date);


--
-- Name: sm_password; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_password ON saq_merchant USING btree (password);


--
-- Name: smpes_date_sent; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smpes_date_sent ON saq_merchant_pci_email_sent USING btree (date_sent);


--
-- Name: smpes_saq_merchant_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smpes_saq_merchant_id ON saq_merchant_pci_email_sent USING btree (saq_merchant_id);


--
-- Name: smpes_saq_merchant_pci_email_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smpes_saq_merchant_pci_email_id ON saq_merchant_pci_email_sent USING btree (saq_merchant_pci_email_id);


--
-- Name: smsx_acknowledgement_name; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_acknowledgement_name ON saq_merchant_survey_xref USING btree (acknowledgement_name);


--
-- Name: smsx_datecomplete; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_datecomplete ON saq_merchant_survey_xref USING btree (datecomplete);


--
-- Name: smsx_saq_confirmation_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_confirmation_survey_id ON saq_merchant_survey_xref USING btree (saq_confirmation_survey_id);


--
-- Name: smsx_saq_eligibility_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_eligibility_survey_id ON saq_merchant_survey_xref USING btree (saq_eligibility_survey_id);


--
-- Name: smsx_saq_merchant_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_merchant_id ON saq_merchant_survey_xref USING btree (saq_merchant_id);


--
-- Name: smsx_saq_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_survey_id ON saq_merchant_survey_xref USING btree (saq_survey_id);


--
-- Name: sp_saq_merchant_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sp_saq_merchant_id ON saq_prequalification USING btree (saq_merchant_id);


--
-- Name: ss_confirmation_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ss_confirmation_survey_id ON saq_survey USING btree (confirmation_survey_id);


--
-- Name: ss_eligibility_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ss_eligibility_survey_id ON saq_survey USING btree (eligibility_survey_id);


--
-- Name: ssqx_saq_question_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ssqx_saq_question_id ON saq_survey_question_xref USING btree (saq_question_id);


--
-- Name: ssqx_saq_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ssqx_saq_survey_id ON saq_survey_question_xref USING btree (saq_survey_id);


--
-- Name: stateaddress; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX stateaddress ON tickler_leads USING btree (state_id);


--
-- Name: status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX status ON tickler_leads_logs USING btree (status_id);


--
-- Name: status_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX status_idx ON onlineapp_applications USING btree (status);


--
-- Name: statuslead; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX statuslead ON tickler_leads USING btree (status_id);


--
-- Name: sys_trans_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sys_trans_merchant_idx ON system_transaction USING btree (merchant_id);


--
-- Name: sys_trans_session_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sys_trans_session_idx ON system_transaction USING btree (session_id);


--
-- Name: system_transaction_chnageid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_chnageid ON system_transaction USING btree (change_id);


--
-- Name: system_transaction_noteid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_noteid ON system_transaction USING btree (merchant_note_id);


--
-- Name: system_transaction_orderid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_orderid ON system_transaction USING btree (order_id);


--
-- Name: system_transaction_programmingi; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_programmingi ON system_transaction USING btree (programming_id);


--
-- Name: system_transaction_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_userid ON system_transaction USING btree (user_id);


--
-- Name: tl_ent_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX tl_ent_merchant_idx ON timeline_entries USING btree (merchant_id);


--
-- Name: tl_timeline_date_completed; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX tl_timeline_date_completed ON timeline_entries USING btree (timeline_date_completed);


--
-- Name: tl_timeline_item; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX tl_timeline_item ON timeline_entries USING btree (timeline_item);


--
-- Name: ubt_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ubt_user_idx ON user_bet_table USING btree (user_id);


--
-- Name: unique_uuid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX unique_uuid ON onlineapp_cobranded_applications USING btree (uuid);


--
-- Name: user_bet_table_pk; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX user_bet_table_pk ON user_bet_table USING btree (bet_code, user_id, network);


--
-- Name: user_email_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_email_idx ON "user" USING btree (user_email);


--
-- Name: user_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_id ON tickler_comments USING btree (user_id);


--
-- Name: user_lastname_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_lastname_idx ON "user" USING btree (user_last_name);


--
-- Name: user_parent_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_parent_user_idx ON "user" USING btree (parent_user);


--
-- Name: user_parent_user_secondary_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_parent_user_secondary_idx ON "user" USING btree (parent_user_secondary);


--
-- Name: user_permission_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_permission_user_idx ON user_permission USING btree (user_id);


--
-- Name: user_username_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_username_idx ON "user" USING btree (username);


--
-- Name: username_key; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX username_key ON onlineapp_multipasses USING btree (username);


--
-- Name: uw_amx_verified_option_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_amx_verified_option_id_index ON uw_approvalinfo_merchant_xref USING btree (verified_option_id);


--
-- Name: uw_approvalinfo_priority_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_approvalinfo_priority_index ON uw_approvalinfo USING btree (priority);


--
-- Name: uw_approvalinfo_verified_type_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_approvalinfo_verified_type_index ON uw_approvalinfo USING btree (verified_type);


--
-- Name: uw_imx_received_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_imx_received_id_index ON uw_infodoc_merchant_xref USING btree (received_id);


--
-- Name: uw_infodoc_priority_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_infodoc_priority_index ON uw_infodoc USING btree (priority);


--
-- Name: uw_infodoc_required_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_infodoc_required_index ON uw_infodoc USING btree (required);


--
-- Name: uw_smx_datetime_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_smx_datetime_index ON uw_status_merchant_xref USING btree (datetime);


--
-- Name: uw_status_priority_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_status_priority_index ON uw_status USING btree (priority);


--
-- Name: uw_verified_option_verified_type_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_verified_option_verified_type_index ON uw_verified_option USING btree (verified_type);


--
-- Name: vendor_rank; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX vendor_rank ON vendor USING btree (rank);


--
-- Name: virtual_check_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX virtual_check_mid ON virtual_check USING btree (vc_mid);


--
-- Name: pci_billing_audit_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER pci_billing_audit_ins AFTER INSERT OR DELETE OR UPDATE ON pci_billing FOR EACH ROW EXECUTE PROCEDURE pci_billing_history_function();


--
-- Name: pci_compliance_status_audit_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER pci_compliance_status_audit_ins AFTER INSERT OR DELETE ON pci_compliance FOR EACH ROW EXECUTE PROCEDURE pci_compliance_status_log_function();


--
-- Name: perform_update_pc_3rd; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_3rd AFTER UPDATE ON merchant_pci FOR EACH ROW EXECUTE PROCEDURE update_pci_compliance_3rd();


--
-- Name: perform_update_pc_ax; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_ax AFTER UPDATE ON saq_merchant_survey_xref FOR EACH ROW EXECUTE PROCEDURE update_pci_compliance_ax();


--
-- Name: perform_update_pc_cs_del; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_del AFTER DELETE ON saq_control_scan FOR EACH ROW EXECUTE PROCEDURE update_pci_compliance_cs_del();


--
-- Name: perform_update_pc_cs_saq_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_saq_ins AFTER INSERT ON saq_control_scan FOR EACH ROW EXECUTE PROCEDURE update_pci_compliance_cs_saq_ins();


--
-- Name: perform_update_pc_cs_scan_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_scan_ins AFTER INSERT ON saq_control_scan FOR EACH ROW EXECUTE PROCEDURE update_pci_compliance_cs_scan_ins();


--
-- Name: perform_update_pc_cs_up; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_up AFTER UPDATE ON saq_control_scan FOR EACH ROW EXECUTE PROCEDURE update_pci_compliance_cs_up();


--
-- Name: bankcard_bc_gw_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY bankcard
    ADD CONSTRAINT bankcard_bc_gw_gateway_id_fkey FOREIGN KEY (bc_gw_gateway_id) REFERENCES gateways(id);


--
-- Name: bankcard_bc_usaepay_rep_gtwy_add_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY bankcard
    ADD CONSTRAINT bankcard_bc_usaepay_rep_gtwy_add_cost_id_fkey FOREIGN KEY (bc_usaepay_rep_gtwy_add_cost_id) REFERENCES usaepay_rep_gtwy_add_cost(id);


--
-- Name: bankcard_bc_usaepay_rep_gtwy_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY bankcard
    ADD CONSTRAINT bankcard_bc_usaepay_rep_gtwy_cost_id_fkey FOREIGN KEY (bc_usaepay_rep_gtwy_cost_id) REFERENCES usaepay_rep_gtwy_cost(id);


--
-- Name: commission_report_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY commission_report
    ADD CONSTRAINT commission_report_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partner(partner_id);


--
-- Name: debit_acquirer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY debit
    ADD CONSTRAINT debit_acquirer_id_fkey FOREIGN KEY (acquirer_id) REFERENCES debit_acquirer(debit_acquirer_id);


--
-- Name: discover_disc_gw_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY discover
    ADD CONSTRAINT discover_disc_gw_gateway_id_fkey FOREIGN KEY (disc_gw_gateway_id) REFERENCES gateways(id);


--
-- Name: discover_user_bet_table_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY discover_user_bet_table
    ADD CONSTRAINT discover_user_bet_table_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user"(user_id);


--
-- Name: equipment_programming_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY equipment_programming
    ADD CONSTRAINT equipment_programming_gateway_id_fkey FOREIGN KEY (gateway_id) REFERENCES gateways(id);


--
-- Name: fk_address_address_type; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY address
    ADD CONSTRAINT fk_address_address_type FOREIGN KEY (address_type) REFERENCES address_type(address_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_address_owner; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY address
    ADD CONSTRAINT fk_address_owner FOREIGN KEY (owner_id) REFERENCES merchant_owner(owner_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_adjustments_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY adjustments
    ADD CONSTRAINT fk_adjustments_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_aev_entity; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY admin_entity_view
    ADD CONSTRAINT fk_aev_entity FOREIGN KEY (entity) REFERENCES entity(entity) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_aev_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY admin_entity_view
    ADD CONSTRAINT fk_aev_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_commission_report_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY commission_report_old
    ADD CONSTRAINT fk_commission_report_merchant FOREIGN KEY (merchant_id_old) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_debit_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY debit
    ADD CONSTRAINT fk_debit_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_ebt_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY ebt
    ADD CONSTRAINT fk_ebt_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_eptx_prog_ig; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY equipment_programming_type_xref
    ADD CONSTRAINT fk_eptx_prog_ig FOREIGN KEY (programming_id) REFERENCES equipment_programming(programming_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_equipment_item_type; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY equipment_item
    ADD CONSTRAINT fk_equipment_item_type FOREIGN KEY (equipment_type) REFERENCES equipment_type(equipment_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_gift_card_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gift_card
    ADD CONSTRAINT fk_gift_card_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_mct_cardtype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_card_types
    ADD CONSTRAINT fk_mct_cardtype FOREIGN KEY (card_type) REFERENCES card_type(card_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_mct_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_card_types
    ADD CONSTRAINT fk_mct_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_change_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_change
    ADD CONSTRAINT fk_merchant_change_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_entity; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT fk_merchant_entity FOREIGN KEY (entity) REFERENCES entity(entity) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_group; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT fk_merchant_group FOREIGN KEY (group_id) REFERENCES "group"(group_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_note_notetype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_note
    ADD CONSTRAINT fk_merchant_note_notetype FOREIGN KEY (note_type) REFERENCES note_type(note_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_note_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_note
    ADD CONSTRAINT fk_merchant_note_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_ref; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT fk_merchant_ref FOREIGN KEY (ref_seq_number) REFERENCES referers(ref_seq_number) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_reference_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reference
    ADD CONSTRAINT fk_merchant_reference_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT fk_merchant_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orderitems_equipitem; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT fk_orderitems_equipitem FOREIGN KEY (equipment_item) REFERENCES equipment_item(equipment_item) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orderitems_equiptype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT fk_orderitems_equiptype FOREIGN KEY (equipment_type) REFERENCES equipment_type(equipment_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orders_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT fk_orders_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orders_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_p_and_s_type; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY products_and_services
    ADD CONSTRAINT fk_p_and_s_type FOREIGN KEY (products_services_type) REFERENCES products_services_type(products_services_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_permission_permgroup; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY permission
    ADD CONSTRAINT fk_permission_permgroup FOREIGN KEY (permission_group) REFERENCES permission_group(permission_group) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_pricing_matrix_userid; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pricing_matrix
    ADD CONSTRAINT fk_pricing_matrix_userid FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_pricing_matrix_usertype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pricing_matrix
    ADD CONSTRAINT fk_pricing_matrix_usertype FOREIGN KEY (user_type) REFERENCES user_type(user_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_rep_cost_structure_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_cost_structure
    ADD CONSTRAINT fk_rep_cost_structure_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_residual_report_[prodtype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_report
    ADD CONSTRAINT "fk_residual_report_[prodtype" FOREIGN KEY (products_services_type) REFERENCES products_services_type(products_services_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_residual_report_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_report
    ADD CONSTRAINT fk_residual_report_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_residual_report_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_report
    ADD CONSTRAINT fk_residual_report_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_sys_trans_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY system_transaction
    ADD CONSTRAINT fk_sys_trans_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_ubt_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY user_bet_table
    ADD CONSTRAINT fk_ubt_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_entity; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT fk_user_entity FOREIGN KEY (entity) REFERENCES entity(entity) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_permission_perm; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY user_permission
    ADD CONSTRAINT fk_user_permission_perm FOREIGN KEY (permission_id) REFERENCES permission(permission_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_permission_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY user_permission
    ADD CONSTRAINT fk_user_permission_user FOREIGN KEY (user_id) REFERENCES "user"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_usertype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT fk_user_usertype FOREIGN KEY (user_type) REFERENCES user_type(user_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_virtual_check_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY virtual_check
    ADD CONSTRAINT fk_virtual_check_merchant FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fkey_saq_answer_survey_question_xref_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_answer
    ADD CONSTRAINT fkey_saq_answer_survey_question_xref_id FOREIGN KEY (saq_survey_question_xref_id) REFERENCES saq_survey_question_xref(id);


--
-- Name: fkey_saq_merchant_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant
    ADD CONSTRAINT fkey_saq_merchant_merchant_id FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: fkey_saq_merchant_survey_xref_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_answer
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_id FOREIGN KEY (saq_merchant_survey_xref_id) REFERENCES saq_merchant_survey_xref(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_confirmation_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xref
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_confirmation_survey_id FOREIGN KEY (saq_confirmation_survey_id) REFERENCES saq_merchant_survey_xref(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_eligibility_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xref
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_eligibility_survey_id FOREIGN KEY (saq_eligibility_survey_id) REFERENCES saq_merchant_survey_xref(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xref
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_merchant_id FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchant(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xref
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_survey_id FOREIGN KEY (saq_survey_id) REFERENCES saq_survey(id);


--
-- Name: fkey_saq_prequalification_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_prequalification
    ADD CONSTRAINT fkey_saq_prequalification_merchant_id FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchant(id);


--
-- Name: fkey_saq_survey_confirmation_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_survey
    ADD CONSTRAINT fkey_saq_survey_confirmation_survey_id FOREIGN KEY (confirmation_survey_id) REFERENCES saq_survey(id);


--
-- Name: fkey_saq_survey_eligibility_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_survey
    ADD CONSTRAINT fkey_saq_survey_eligibility_survey_id FOREIGN KEY (eligibility_survey_id) REFERENCES saq_survey(id);


--
-- Name: fkey_saq_survey_question_xref_saq_question_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_survey_question_xref
    ADD CONSTRAINT fkey_saq_survey_question_xref_saq_question_id FOREIGN KEY (saq_question_id) REFERENCES saq_question(id);


--
-- Name: fkey_saq_survey_question_xref_saq_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_survey_question_xref
    ADD CONSTRAINT fkey_saq_survey_question_xref_saq_survey_id FOREIGN KEY (saq_survey_id) REFERENCES saq_survey(id);


--
-- Name: gateway1_gw1_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway1
    ADD CONSTRAINT gateway1_gw1_gateway_id_fkey FOREIGN KEY (gw1_gateway_id) REFERENCES gateways(id);


--
-- Name: gateway2_gw2_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway2
    ADD CONSTRAINT gateway2_gw2_gateway_id_fkey FOREIGN KEY (gw2_gateway_id) REFERENCES gateways(id);


--
-- Name: gateway_gw_usaepay_rep_gtwy_add_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway
    ADD CONSTRAINT gateway_gw_usaepay_rep_gtwy_add_cost_id_fkey FOREIGN KEY (gw_usaepay_rep_gtwy_add_cost_id) REFERENCES usaepay_rep_gtwy_add_cost(id);


--
-- Name: gateway_gw_usaepay_rep_gtwy_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway
    ADD CONSTRAINT gateway_gw_usaepay_rep_gtwy_cost_id_fkey FOREIGN KEY (gw_usaepay_rep_gtwy_cost_id) REFERENCES usaepay_rep_gtwy_cost(id);


--
-- Name: merchant_acquirer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT merchant_acquirer_id_fkey FOREIGN KEY (acquirer_id) REFERENCES merchant_acquirer(acquirer_id);


--
-- Name: merchant_bin_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT merchant_bin_id_fkey FOREIGN KEY (bin_id) REFERENCES merchant_bin(bin_id);


--
-- Name: merchant_cancellation_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_cancellation
    ADD CONSTRAINT merchant_cancellation_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: merchant_cancellation_subreason_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_cancellation
    ADD CONSTRAINT merchant_cancellation_subreason_id_fkey FOREIGN KEY (subreason_id) REFERENCES merchant_cancellation_subreason(id);


--
-- Name: merchant_cobranded_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT merchant_cobranded_application_id_fkey FOREIGN KEY (cobranded_application_id) REFERENCES onlineapp_cobranded_applications(id);


--
-- Name: merchant_onlineapp_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT merchant_onlineapp_application_id_fkey FOREIGN KEY (onlineapp_application_id) REFERENCES onlineapp_applications(id);


--
-- Name: merchant_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant
    ADD CONSTRAINT merchant_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partner(partner_id);


--
-- Name: merchant_pci_merchant_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_pci
    ADD CONSTRAINT merchant_pci_merchant_id_fk FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: merchant_reject_line_rejectid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reject_line
    ADD CONSTRAINT merchant_reject_line_rejectid_fkey FOREIGN KEY (rejectid) REFERENCES merchant_reject(id);


--
-- Name: merchant_reject_line_statusid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reject_line
    ADD CONSTRAINT merchant_reject_line_statusid_fkey FOREIGN KEY (statusid) REFERENCES merchant_reject_status(id);


--
-- Name: merchant_reject_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reject
    ADD CONSTRAINT merchant_reject_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: merchant_reject_recurranceid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reject
    ADD CONSTRAINT merchant_reject_recurranceid_fkey FOREIGN KEY (recurranceid) REFERENCES merchant_reject_recurrance(id);


--
-- Name: merchant_reject_typeid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reject
    ADD CONSTRAINT merchant_reject_typeid_fkey FOREIGN KEY (typeid) REFERENCES merchant_reject_type(id);


--
-- Name: merchant_uw_final_approved_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_uw
    ADD CONSTRAINT merchant_uw_final_approved_id_fkey FOREIGN KEY (final_approved_id) REFERENCES merchant_uw_final_approved(id);


--
-- Name: merchant_uw_final_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_uw
    ADD CONSTRAINT merchant_uw_final_status_id_fkey FOREIGN KEY (final_status_id) REFERENCES merchant_uw_final_status(id);


--
-- Name: merchant_uw_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_uw
    ADD CONSTRAINT merchant_uw_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: onlineapp_apips_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_apips
    ADD CONSTRAINT onlineapp_apips_user_id_fkey FOREIGN KEY (user_id) REFERENCES onlineapp_users(id);


--
-- Name: onlineapp_applications_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_applications
    ADD CONSTRAINT onlineapp_applications_user_id_fkey FOREIGN KEY (user_id) REFERENCES onlineapp_users(id);


--
-- Name: onlineapp_cobranded_applications_applications_values_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobranded_application_values
    ADD CONSTRAINT onlineapp_cobranded_applications_applications_values_fk FOREIGN KEY (cobranded_application_id) REFERENCES onlineapp_cobranded_applications(id);


--
-- Name: onlineapp_coversheets_cobranded_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_coversheets
    ADD CONSTRAINT onlineapp_coversheets_cobranded_application_id_fkey FOREIGN KEY (cobranded_application_id) REFERENCES onlineapp_cobranded_applications(id);


--
-- Name: onlineapp_coversheets_onlineapp_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_coversheets
    ADD CONSTRAINT onlineapp_coversheets_onlineapp_application_id_fkey FOREIGN KEY (onlineapp_application_id) REFERENCES onlineapp_applications(id);


--
-- Name: onlineapp_coversheets_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_coversheets
    ADD CONSTRAINT onlineapp_coversheets_user_id_fkey FOREIGN KEY (user_id) REFERENCES onlineapp_users(id);


--
-- Name: onlineapp_email_timelines_app_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_email_timelines
    ADD CONSTRAINT onlineapp_email_timelines_app_id_fkey FOREIGN KEY (app_id) REFERENCES onlineapp_applications(id);


--
-- Name: onlineapp_email_timelines_cobranded_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_email_timelines
    ADD CONSTRAINT onlineapp_email_timelines_cobranded_application_id_fkey FOREIGN KEY (cobranded_application_id) REFERENCES onlineapp_cobranded_applications(id);


--
-- Name: onlineapp_email_timelines_email_timeline_subject_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_email_timelines
    ADD CONSTRAINT onlineapp_email_timelines_email_timeline_subject_id_fkey FOREIGN KEY (email_timeline_subject_id) REFERENCES onlineapp_email_timeline_subjects(id);


--
-- Name: onlineapp_epayments_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_application_id_fkey FOREIGN KEY (onlineapp_application_id) REFERENCES onlineapp_applications(id);


--
-- Name: onlineapp_epayments_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: onlineapp_epayments_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_user_id_fkey FOREIGN KEY (user_id) REFERENCES onlineapp_users(id);


--
-- Name: onlineapp_template_cobrand_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_templates
    ADD CONSTRAINT onlineapp_template_cobrand_fk FOREIGN KEY (cobrand_id) REFERENCES onlineapp_cobrands(id);


--
-- Name: onlineapp_template_field_section_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_template_fields
    ADD CONSTRAINT onlineapp_template_field_section_fk FOREIGN KEY (section_id) REFERENCES onlineapp_template_sections(id);


--
-- Name: onlineapp_template_fields_applications_values_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobranded_application_values
    ADD CONSTRAINT onlineapp_template_fields_applications_values_fk FOREIGN KEY (template_field_id) REFERENCES onlineapp_template_fields(id);


--
-- Name: onlineapp_template_page_template_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_template_pages
    ADD CONSTRAINT onlineapp_template_page_template_fk FOREIGN KEY (template_id) REFERENCES onlineapp_templates(id);


--
-- Name: onlineapp_template_section_page_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_template_sections
    ADD CONSTRAINT onlineapp_template_section_page_fk FOREIGN KEY (page_id) REFERENCES onlineapp_template_pages(id);


--
-- Name: onlineapp_templates_cobranded_applications_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobranded_applications
    ADD CONSTRAINT onlineapp_templates_cobranded_applications_fk FOREIGN KEY (template_id) REFERENCES onlineapp_templates(id);


--
-- Name: onlineapp_users_cobrand_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_users
    ADD CONSTRAINT onlineapp_users_cobrand_fk FOREIGN KEY (cobrand_id) REFERENCES onlineapp_cobrands(id);


--
-- Name: onlineapp_users_cobranded_applications_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_cobranded_applications
    ADD CONSTRAINT onlineapp_users_cobranded_applications_fk FOREIGN KEY (user_id) REFERENCES onlineapp_users(id);


--
-- Name: onlineapp_users_group_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_users
    ADD CONSTRAINT onlineapp_users_group_id_fkey FOREIGN KEY (group_id) REFERENCES onlineapp_groups(id);


--
-- Name: onlineapp_users_template_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_users
    ADD CONSTRAINT onlineapp_users_template_fk FOREIGN KEY (template_id) REFERENCES onlineapp_templates(id) ON DELETE SET NULL;


--
-- Name: orderitems_replacement_orderitem_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems_replacement
    ADD CONSTRAINT orderitems_replacement_orderitem_id_fkey FOREIGN KEY (orderitem_id) REFERENCES orderitems(orderitem_id);


--
-- Name: orderitems_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_type_id_fkey FOREIGN KEY (type_id) REFERENCES orderitem_type(orderitem_type_id);


--
-- Name: pci_billing_pci_billing_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_billing
    ADD CONSTRAINT pci_billing_pci_billing_type_id_fkey FOREIGN KEY (pci_billing_type_id) REFERENCES pci_billing_type(id);


--
-- Name: pci_compliance_pci_compliance_date_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_compliance
    ADD CONSTRAINT pci_compliance_pci_compliance_date_type_id_fkey FOREIGN KEY (pci_compliance_date_type_id) REFERENCES pci_compliance_date_type(id);


--
-- Name: pci_compliance_saq_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_compliance
    ADD CONSTRAINT pci_compliance_saq_merchant_id_fkey FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchant(id);


--
-- Name: referer_products_services_xref_products_services_type_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY referer_products_services_xref
    ADD CONSTRAINT referer_products_services_xref_products_services_type_fkey FOREIGN KEY (products_services_type) REFERENCES products_services_type(products_services_type);


--
-- Name: referer_products_services_xref_ref_seq_number_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY referer_products_services_xref
    ADD CONSTRAINT referer_products_services_xref_ref_seq_number_fkey FOREIGN KEY (ref_seq_number) REFERENCES referers(ref_seq_number);


--
-- Name: referers_bet_ref_seq_number_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY referers_bet
    ADD CONSTRAINT referers_bet_ref_seq_number_fkey FOREIGN KEY (ref_seq_number) REFERENCES referers(ref_seq_number);


--
-- Name: rep_partner_xref_mgr1_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xref
    ADD CONSTRAINT rep_partner_xref_mgr1_id_fkey FOREIGN KEY (mgr1_id) REFERENCES "user"(user_id);


--
-- Name: rep_partner_xref_mgr2_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xref
    ADD CONSTRAINT rep_partner_xref_mgr2_id_fkey FOREIGN KEY (mgr2_id) REFERENCES "user"(user_id);


--
-- Name: rep_partner_xref_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xref
    ADD CONSTRAINT rep_partner_xref_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partner(partner_id);


--
-- Name: rep_partner_xref_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xref
    ADD CONSTRAINT rep_partner_xref_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user"(user_id);


--
-- Name: rep_product_profit_pct_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_product_profit_pct
    ADD CONSTRAINT rep_product_profit_pct_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user"(user_id);


--
-- Name: residual_report_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_report
    ADD CONSTRAINT residual_report_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partner(partner_id);


--
-- Name: saq_merchant_pci_email_sent_saq_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_pci_email_sent
    ADD CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_id_fkey FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchant(id);


--
-- Name: saq_merchant_pci_email_sent_saq_merchant_pci_email_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_pci_email_sent
    ADD CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_pci_email_id_fkey FOREIGN KEY (saq_merchant_pci_email_id) REFERENCES saq_merchant_pci_email(id);


--
-- Name: user_parent_user_secondary_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_parent_user_secondary_fkey FOREIGN KEY (parent_user_secondary) REFERENCES "user"(user_id);


--
-- Name: uw_approvalinfo_merchant_xref_approvalinfo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_approvalinfo_merchant_xref
    ADD CONSTRAINT uw_approvalinfo_merchant_xref_approvalinfo_id_fkey FOREIGN KEY (approvalinfo_id) REFERENCES uw_approvalinfo(id);


--
-- Name: uw_approvalinfo_merchant_xref_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_approvalinfo_merchant_xref
    ADD CONSTRAINT uw_approvalinfo_merchant_xref_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: uw_approvalinfo_merchant_xref_verified_option_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_approvalinfo_merchant_xref
    ADD CONSTRAINT uw_approvalinfo_merchant_xref_verified_option_id_fkey FOREIGN KEY (verified_option_id) REFERENCES uw_verified_option(id);


--
-- Name: uw_infodoc_merchant_xref_infodoc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_infodoc_merchant_xref
    ADD CONSTRAINT uw_infodoc_merchant_xref_infodoc_id_fkey FOREIGN KEY (infodoc_id) REFERENCES uw_infodoc(id);


--
-- Name: uw_infodoc_merchant_xref_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_infodoc_merchant_xref
    ADD CONSTRAINT uw_infodoc_merchant_xref_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: uw_infodoc_merchant_xref_received_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_infodoc_merchant_xref
    ADD CONSTRAINT uw_infodoc_merchant_xref_received_id_fkey FOREIGN KEY (received_id) REFERENCES uw_received(id);


--
-- Name: uw_status_merchant_xref_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_status_merchant_xref
    ADD CONSTRAINT uw_status_merchant_xref_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchant(merchant_id);


--
-- Name: uw_status_merchant_xref_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_status_merchant_xref
    ADD CONSTRAINT uw_status_merchant_xref_status_id_fkey FOREIGN KEY (status_id) REFERENCES uw_status(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: ach; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE ach FROM PUBLIC;
REVOKE ALL ON TABLE ach FROM axia;
GRANT ALL ON TABLE ach TO axia;


--
-- Name: address; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE address FROM PUBLIC;
REVOKE ALL ON TABLE address FROM axia;
GRANT ALL ON TABLE address TO axia;


--
-- Name: address_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE address_type FROM PUBLIC;
REVOKE ALL ON TABLE address_type FROM axia;
GRANT ALL ON TABLE address_type TO axia;


--
-- Name: adjustments; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE adjustments FROM PUBLIC;
REVOKE ALL ON TABLE adjustments FROM axia;
GRANT ALL ON TABLE adjustments TO axia;


--
-- Name: adjustments_t; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE adjustments_t FROM PUBLIC;
REVOKE ALL ON TABLE adjustments_t FROM axia;
GRANT ALL ON TABLE adjustments_t TO axia;


--
-- Name: admin_entity_view; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE admin_entity_view FROM PUBLIC;
REVOKE ALL ON TABLE admin_entity_view FROM axia;
GRANT ALL ON TABLE admin_entity_view TO axia;


--
-- Name: bankcard; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE bankcard FROM PUBLIC;
REVOKE ALL ON TABLE bankcard FROM axia;
GRANT ALL ON TABLE bankcard TO axia;


--
-- Name: bet; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE bet FROM PUBLIC;
REVOKE ALL ON TABLE bet FROM axia;
GRANT ALL ON TABLE bet TO axia;


--
-- Name: card_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE card_type FROM PUBLIC;
REVOKE ALL ON TABLE card_type FROM axia;
GRANT ALL ON TABLE card_type TO axia;


--
-- Name: change_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE change_type FROM PUBLIC;
REVOKE ALL ON TABLE change_type FROM axia;
GRANT ALL ON TABLE change_type TO axia;


--
-- Name: check_guarantee; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE check_guarantee FROM PUBLIC;
REVOKE ALL ON TABLE check_guarantee FROM axia;
GRANT ALL ON TABLE check_guarantee TO axia;


--
-- Name: commission_report_old; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE commission_report_old FROM PUBLIC;
REVOKE ALL ON TABLE commission_report_old FROM axia;
GRANT ALL ON TABLE commission_report_old TO axia;


--
-- Name: merchant; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant FROM PUBLIC;
REVOKE ALL ON TABLE merchant FROM axia;
GRANT ALL ON TABLE merchant TO axia;


--
-- Name: timeline_entries; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE timeline_entries FROM PUBLIC;
REVOKE ALL ON TABLE timeline_entries FROM axia;
GRANT ALL ON TABLE timeline_entries TO axia;


--
-- Name: debit; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE debit FROM PUBLIC;
REVOKE ALL ON TABLE debit FROM axia;
GRANT ALL ON TABLE debit TO axia;


--
-- Name: ebt; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE ebt FROM PUBLIC;
REVOKE ALL ON TABLE ebt FROM axia;
GRANT ALL ON TABLE ebt TO axia;


--
-- Name: entity; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE entity FROM PUBLIC;
REVOKE ALL ON TABLE entity FROM axia;
GRANT ALL ON TABLE entity TO axia;


--
-- Name: equipment_item; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_item FROM PUBLIC;
REVOKE ALL ON TABLE equipment_item FROM axia;
GRANT ALL ON TABLE equipment_item TO axia;


--
-- Name: equipment_programming; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_programming FROM PUBLIC;
REVOKE ALL ON TABLE equipment_programming FROM axia;
GRANT ALL ON TABLE equipment_programming TO axia;


--
-- Name: equipment_programming_type_xref; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_programming_type_xref FROM PUBLIC;
REVOKE ALL ON TABLE equipment_programming_type_xref FROM axia;
GRANT ALL ON TABLE equipment_programming_type_xref TO axia;


--
-- Name: equipment_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_type FROM PUBLIC;
REVOKE ALL ON TABLE equipment_type FROM axia;
GRANT ALL ON TABLE equipment_type TO axia;


--
-- Name: gift_card; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE gift_card FROM PUBLIC;
REVOKE ALL ON TABLE gift_card FROM axia;
GRANT ALL ON TABLE gift_card TO axia;


--
-- Name: group; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE "group" FROM PUBLIC;
REVOKE ALL ON TABLE "group" FROM axia;
GRANT ALL ON TABLE "group" TO axia;


--
-- Name: merchant_ach; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_ach FROM PUBLIC;
REVOKE ALL ON TABLE merchant_ach FROM axia;
GRANT ALL ON TABLE merchant_ach TO axia;


--
-- Name: merchant_ach_app_status; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_ach_app_status FROM PUBLIC;
REVOKE ALL ON TABLE merchant_ach_app_status FROM axia;
GRANT ALL ON TABLE merchant_ach_app_status TO axia;


--
-- Name: merchant_ach_billing_option; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_ach_billing_option FROM PUBLIC;
REVOKE ALL ON TABLE merchant_ach_billing_option FROM axia;
GRANT ALL ON TABLE merchant_ach_billing_option TO axia;


--
-- Name: merchant_bank; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_bank FROM PUBLIC;
REVOKE ALL ON TABLE merchant_bank FROM axia;
GRANT ALL ON TABLE merchant_bank TO axia;


--
-- Name: merchant_card_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_card_types FROM PUBLIC;
REVOKE ALL ON TABLE merchant_card_types FROM axia;
GRANT ALL ON TABLE merchant_card_types TO axia;


--
-- Name: merchant_change; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_change FROM PUBLIC;
REVOKE ALL ON TABLE merchant_change FROM axia;
GRANT ALL ON TABLE merchant_change TO axia;


--
-- Name: merchant_note; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_note FROM PUBLIC;
REVOKE ALL ON TABLE merchant_note FROM axia;
GRANT ALL ON TABLE merchant_note TO axia;


--
-- Name: merchant_owner; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_owner FROM PUBLIC;
REVOKE ALL ON TABLE merchant_owner FROM axia;
GRANT ALL ON TABLE merchant_owner TO axia;


--
-- Name: merchant_reference; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_reference FROM PUBLIC;
REVOKE ALL ON TABLE merchant_reference FROM axia;
GRANT ALL ON TABLE merchant_reference TO axia;


--
-- Name: merchant_reference_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_reference_type FROM PUBLIC;
REVOKE ALL ON TABLE merchant_reference_type FROM axia;
GRANT ALL ON TABLE merchant_reference_type TO axia;


--
-- Name: network; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE network FROM PUBLIC;
REVOKE ALL ON TABLE network FROM axia;
GRANT ALL ON TABLE network TO axia;


--
-- Name: note_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE note_type FROM PUBLIC;
REVOKE ALL ON TABLE note_type FROM axia;
GRANT ALL ON TABLE note_type TO axia;


--
-- Name: orderitems; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE orderitems FROM PUBLIC;
REVOKE ALL ON TABLE orderitems FROM axia;
GRANT ALL ON TABLE orderitems TO axia;


--
-- Name: orders; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE orders FROM PUBLIC;
REVOKE ALL ON TABLE orders FROM axia;
GRANT ALL ON TABLE orders TO axia;


--
-- Name: permission; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE permission FROM PUBLIC;
REVOKE ALL ON TABLE permission FROM axia;
GRANT ALL ON TABLE permission TO axia;


--
-- Name: permission_group; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE permission_group FROM PUBLIC;
REVOKE ALL ON TABLE permission_group FROM axia;
GRANT ALL ON TABLE permission_group TO axia;


--
-- Name: pricing_matrix; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE pricing_matrix FROM PUBLIC;
REVOKE ALL ON TABLE pricing_matrix FROM axia;
GRANT ALL ON TABLE pricing_matrix TO axia;


--
-- Name: products_and_services; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE products_and_services FROM PUBLIC;
REVOKE ALL ON TABLE products_and_services FROM axia;
GRANT ALL ON TABLE products_and_services TO axia;


--
-- Name: products_services_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE products_services_type FROM PUBLIC;
REVOKE ALL ON TABLE products_services_type FROM axia;
GRANT ALL ON TABLE products_services_type TO axia;


--
-- Name: referers; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE referers FROM PUBLIC;
REVOKE ALL ON TABLE referers FROM axia;
GRANT ALL ON TABLE referers TO axia;


--
-- Name: rep_cost_structure; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE rep_cost_structure FROM PUBLIC;
REVOKE ALL ON TABLE rep_cost_structure FROM axia;
GRANT ALL ON TABLE rep_cost_structure TO axia;


--
-- Name: residual_pricing; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE residual_pricing FROM PUBLIC;
REVOKE ALL ON TABLE residual_pricing FROM axia;
GRANT ALL ON TABLE residual_pricing TO axia;


--
-- Name: residual_report; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE residual_report FROM PUBLIC;
REVOKE ALL ON TABLE residual_report FROM axia;
GRANT ALL ON TABLE residual_report TO axia;


--
-- Name: sales_goal; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE sales_goal FROM PUBLIC;
REVOKE ALL ON TABLE sales_goal FROM axia;
GRANT ALL ON TABLE sales_goal TO axia;


--
-- Name: sales_goal_archive; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE sales_goal_archive FROM PUBLIC;
REVOKE ALL ON TABLE sales_goal_archive FROM axia;
GRANT ALL ON TABLE sales_goal_archive TO axia;


--
-- Name: shipping_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE shipping_type FROM PUBLIC;
REVOKE ALL ON TABLE shipping_type FROM axia;
GRANT ALL ON TABLE shipping_type TO axia;


--
-- Name: system_transaction; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE system_transaction FROM PUBLIC;
REVOKE ALL ON TABLE system_transaction FROM axia;
GRANT ALL ON TABLE system_transaction TO axia;


--
-- Name: timeline_item; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE timeline_item FROM PUBLIC;
REVOKE ALL ON TABLE timeline_item FROM axia;
GRANT ALL ON TABLE timeline_item TO axia;


--
-- Name: transaction_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE transaction_type FROM PUBLIC;
REVOKE ALL ON TABLE transaction_type FROM axia;
GRANT ALL ON TABLE transaction_type TO axia;


--
-- Name: user; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE "user" FROM PUBLIC;
REVOKE ALL ON TABLE "user" FROM axia;
GRANT ALL ON TABLE "user" TO axia;


--
-- Name: user_bet_table; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE user_bet_table FROM PUBLIC;
REVOKE ALL ON TABLE user_bet_table FROM axia;
GRANT ALL ON TABLE user_bet_table TO axia;


--
-- Name: user_permission; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE user_permission FROM PUBLIC;
REVOKE ALL ON TABLE user_permission FROM axia;
GRANT ALL ON TABLE user_permission TO axia;


--
-- Name: user_type; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE user_type FROM PUBLIC;
REVOKE ALL ON TABLE user_type FROM axia;
GRANT ALL ON TABLE user_type TO axia;


--
-- Name: vendor; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE vendor FROM PUBLIC;
REVOKE ALL ON TABLE vendor FROM axia;
GRANT ALL ON TABLE vendor TO axia;


--
-- Name: virtual_check; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE virtual_check FROM PUBLIC;
REVOKE ALL ON TABLE virtual_check FROM axia;
GRANT ALL ON TABLE virtual_check TO axia;


--
-- Name: virtual_check_web; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE virtual_check_web FROM PUBLIC;
REVOKE ALL ON TABLE virtual_check_web FROM axia;
GRANT ALL ON TABLE virtual_check_web TO axia;


--
-- PostgreSQL database dump complete
--

