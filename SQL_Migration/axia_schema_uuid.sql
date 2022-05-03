--
-- PostgreSQL database dump
--


SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--
 
CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO postgres;

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
-- Name: commission_pricings; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE commission_pricings (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.commission_pricings OWNER TO axia;

--
-- Name: achs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE achs (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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
    ach_add_bank_orig_ident_fee real,
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


ALTER TABLE public.achs OWNER TO axia;

--
-- Name: addresses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE addresses (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.addresses OWNER TO axia;

--
-- Name: address_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE address_types (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    address_type character varying(10) NOT NULL,
    address_type_description character varying(50)
);


ALTER TABLE public.address_types OWNER TO axia;

--
-- Name: adjustments; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE adjustments (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    adj_seq_number integer NOT NULL,
    user_id integer NOT NULL,
    adj_date date,
    adj_description character varying(100),
    adj_amount real
);



--
-- Name: admin_entity_views; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE admin_entity_views (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    user_id integer NOT NULL,
    entity character varying(10) NOT NULL
);


ALTER TABLE public.admin_entity_views OWNER TO axia;

--
-- Name: amexes; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE amexes (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    amex_processing_rate real,
    amex_per_item_fee real
);


ALTER TABLE public.amexes OWNER TO axia;

--
-- Name: authorizes; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE authorizes (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    mid character varying(20),
    transaction_fee real,
    monthly_fee real
);


ALTER TABLE public.authorizes OWNER TO axia;

--
-- Name: saq_merchant_survey_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchant_survey_xrefs (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.saq_merchant_survey_xrefs OWNER TO axia;

--
-- Name: axia_compliants; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW axia_compliants AS
    SELECT smsx.saq_merchant_id, smsx.datecomplete FROM saq_merchant_survey_xrefs smsx WHERE (smsx.acknowledgement_name IS NOT NULL);


ALTER TABLE public.axia_compliants OWNER TO axia;

--
-- Name: bankcards; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE bankcards (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.bankcards OWNER TO axia;

--
-- Name: bets; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE bets (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.bets OWNER TO axia;

--
-- Name: cancellation_fees; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE cancellation_fees (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    cancellation_fee_id integer NOT NULL,
    cancellation_fee_description character varying(50) NOT NULL
);


ALTER TABLE public.cancellation_fees OWNER TO axia;

--
-- Name: card_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE card_types (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    card_type character varying(10) NOT NULL,
    card_type_description character varying(20)
);


ALTER TABLE public.card_types OWNER TO axia;

--
-- Name: change_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE change_types (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    change_type integer NOT NULL,
    change_type_description character varying(50)
);


ALTER TABLE public.change_types OWNER TO axia;

--
-- Name: check_guarantees; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE check_guarantees (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.check_guarantees OWNER TO axia;

--
-- Name: commission_reports; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE commission_reports (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.commission_reports OWNER TO axia;

--
-- Name: commission_report_olds; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE commission_report_olds (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.commission_report_olds OWNER TO axia;

--
-- Name: merchants; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchants (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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
    partner_exclude_volume boolean
);


ALTER TABLE public.merchants OWNER TO axia;

--
-- Name: merchant_cancellations; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_cancellations (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.merchant_cancellations OWNER TO axia;

--
-- Name: merchant_pcis; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_pcis (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    compliance_level integer DEFAULT 4 NOT NULL,
    saq_completed_date date,
    compliance_fee double precision,
    insurance_fee double precision,
    last_security_scan date,
    scanning_company character varying(255)
);


ALTER TABLE public.merchant_pcis OWNER TO axia;

--
-- Name: saq_control_scans; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_control_scans (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.saq_control_scans OWNER TO axia;

--
-- Name: saq_merchants; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchants (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    merchant_name character varying(255) NOT NULL,
    merchant_email character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    email_sent timestamp without time zone,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone
);


ALTER TABLE public.saq_merchants OWNER TO axia;

--
-- Name: timeline_entries; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE timeline_entries (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    timeline_item character varying(50) NOT NULL,
    timeline_date_completed date,
    action_flag boolean DEFAULT false
);


ALTER TABLE public.timeline_entries OWNER TO axia;

--
-- Name: saq_eligibles; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW saq_eligibles AS
    SELECT sm.id, sm.merchant_id, sm.merchant_email, sm.merchant_name, sm.password, sm.email_sent, scs.saq_type, scs.first_scan_date, scs.first_questionnaire_date, scs.scan_status, scs.questionnaire_status, scs.pci_compliance, scs.creation_date, scs.sua, mp.saq_completed_date, sm.billing_date, sm.next_billing_date, scs.quarterly_scan_fee, mp.compliance_fee, mp.insurance_fee FROM (((((((saq_merchants sm LEFT JOIN merchants m ON ((((sm.merchant_id)::text = (m.merchant_id)::text) AND (m.active = 1)))) LEFT JOIN merchant_pcis mp ON (((sm.merchant_id)::text = (mp.merchant_id)::text))) LEFT JOIN saq_control_scans scs ON ((substr((sm.merchant_id)::text, 5, 12) = substr((scs.merchant_id)::text, 5, 12)))) LEFT JOIN merchant_cancellations mc ON ((((sm.merchant_id)::text = (mc.merchant_id)::text) AND (mc.date_completed IS NOT NULL)))) LEFT JOIN timeline_entries tes ON ((((sm.merchant_id)::text = (tes.merchant_id)::text) AND (((tes.timeline_item)::text = 'SUB'::text) AND (tes.timeline_date_completed < '2010-08-01'::date))))) LEFT JOIN timeline_entries tei ON ((((sm.merchant_id)::text = (tei.merchant_id)::text) AND ((tei.timeline_item)::text = 'INS'::text)))) LEFT JOIN bankcards b ON (((sm.merchant_id)::text = (b.merchant_id)::text))) WHERE ((((((sm.merchant_email)::text ~~ '%@%'::text) AND (m.merchant_id IS NOT NULL)) AND ((tei.merchant_id IS NOT NULL) OR (tes.merchant_id IS NOT NULL))) AND (b.merchant_id IS NOT NULL)) AND (mc.merchant_id IS NULL));


ALTER TABLE public.saq_eligibles OWNER TO axia;

--
-- Name: saq_surveys; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_surveys (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(255) NOT NULL,
    saq_level character varying(4),
    eligibility_survey_id integer,
    confirmation_survey_id integer
);


ALTER TABLE public.saq_surveys OWNER TO axia;

--
-- Name: compliant_merchants; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW compliant_merchants AS
    SELECT se.id, se.merchant_id, se.merchant_email, se.merchant_name, se.password, se.email_sent, se.saq_type, se.first_scan_date, se.first_questionnaire_date, se.scan_status, se.questionnaire_status, se.pci_compliance, se.creation_date, se.sua, se.saq_completed_date, se.billing_date, se.next_billing_date, se.quarterly_scan_fee, se.compliance_fee, se.insurance_fee, smsx.datecomplete, ss.saq_level FROM ((saq_eligibles se LEFT JOIN saq_merchant_survey_xrefs smsx ON (((se.id = smsx.saq_merchant_id) AND (smsx.acknowledgement_name IS NOT NULL)))) LEFT JOIN saq_surveys ss ON ((ss.id = smsx.saq_survey_id))) WHERE ((((se.pci_compliance)::text = 'Yes'::text) OR ((se.saq_completed_date IS NOT NULL) AND (se.saq_completed_date > (now() - '1 year'::interval)))) OR ((smsx.datecomplete IS NOT NULL) AND (smsx.datecomplete > (now() - '1 year'::interval))));


ALTER TABLE public.compliant_merchants OWNER TO axia;

--
-- Name: compliant_merchants_cs; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW compliant_merchants_cs AS
    SELECT se.id, se.merchant_id, se.merchant_email, se.merchant_name, se.password, se.email_sent, se.saq_type, se.first_scan_date, se.first_questionnaire_date, se.scan_status, se.questionnaire_status, se.pci_compliance, se.creation_date, se.sua, se.saq_completed_date, se.billing_date, se.next_billing_date, se.quarterly_scan_fee, se.compliance_fee, se.insurance_fee FROM saq_eligibles se WHERE ((se.pci_compliance)::text = 'Yes'::text);


ALTER TABLE public.compliant_merchants_cs OWNER TO axia;

--
-- Name: compliant_merchants_mvs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE compliant_merchants_mvs (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.compliant_merchants_mvs OWNER TO axia;

--
-- Name: debits; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE debits (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    mid character varying(20),
    transaction_fee real,
    monthly_fee real,
    monthly_volume real,
    monthly_num_items real,
    rate_pct real,
    acquirer_id integer
);


ALTER TABLE public.debits OWNER TO axia;

--
-- Name: debit_acquirers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE debit_acquirers (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    debit_acquirer_id integer NOT NULL,
    debit_acquirers character varying(137) NOT NULL
);


ALTER TABLE public.debit_acquirers OWNER TO axia;

--
-- Name: discovers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE discovers (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.discovers OWNER TO axia;

--
-- Name: discover_bets; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE discover_bets (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    bet_code character varying(50) NOT NULL,
    bet_extra_pct real
);


ALTER TABLE public.discover_bets OWNER TO axia;

--
-- Name: discover_user_bet_tables; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE discover_user_bet_tables (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    bet_code character varying(50) NOT NULL,
    user_id integer NOT NULL,
    network character varying(20) NOT NULL,
    rate real,
    pi real
);


ALTER TABLE public.discover_user_bet_tables OWNER TO axia;

--
-- Name: ebts; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE ebts (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    mid character varying(20),
    transaction_fee real,
    monthly_fee real
);


ALTER TABLE public.ebts OWNER TO axia;

--
-- Name: entities; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE entities (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    entity character varying(10) NOT NULL,
    entity_name character varying(50)
);


ALTER TABLE public.entities OWNER TO axia;

--
-- Name: equipment_items; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_items (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    equipment_item integer NOT NULL,
    equipment_type character varying(10) NOT NULL,
    equipment_item_description character varying(100),
    equipment_item_true_price real,
    equipment_item_rep_price real,
    active integer,
    warranty integer
);


ALTER TABLE public.equipment_items OWNER TO axia;

--
-- Name: equipment_programmings; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_programmings (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.equipment_programmings OWNER TO axia;

--
-- Name: equipment_programming_type_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_programming_type_xrefs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    programming_id integer NOT NULL,
    programming_type character varying(10) NOT NULL
);


ALTER TABLE public.equipment_programming_type_xrefs OWNER TO axia;

--
-- Name: equipment_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE equipment_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    equipment_type character varying(10) NOT NULL,
    equipment_type_description character varying(50)
);


ALTER TABLE public.equipment_types OWNER TO axia;

--
-- Name: gateway0s; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gateway0s (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    gw_rate real,
    gw_per_item real,
    gw_statement real,
    gw_epay_retail_num integer,
    gw_usaepay_rep_gtwy_cost_id integer,
    gw_usaepay_rep_gtwy_add_cost_id integer
);


ALTER TABLE public.gateway0s OWNER TO axia;

--
-- Name: gateway1s; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gateway1s (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.gateway1s OWNER TO axia;

--
-- Name: gateway2; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gateway2 (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(57) NOT NULL
);


ALTER TABLE public.gateways OWNER TO axia;

--
-- Name: gift_cards; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE gift_cards (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.gift_cards OWNER TO axia;

--
-- Name: groups; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE "groups" (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    group_id character varying(10) NOT NULL,
    group_description character varying(50),
    active boolean DEFAULT true NOT NULL
);


ALTER TABLE public."groups" OWNER TO axia;

--
-- Name: last_deposit_reports; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE last_deposit_reports (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50),
    merchant_dba character varying(100),
    last_deposit_date date,
    user_id integer,
    monthly_volume numeric(10,2)
);


ALTER TABLE public.last_deposit_reports OWNER TO axia;

--
-- Name: merchant_achs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_achs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.merchant_achs OWNER TO axia;

--
-- Name: merchant_ach_app_statuses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_ach_app_statuses (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    app_status_id integer NOT NULL,
    app_status_description character varying(50) NOT NULL,
    rank integer,
    app_true_price real,
    app_rep_price real
);


ALTER TABLE public.merchant_ach_app_statuses OWNER TO axia;

--
-- Name: merchant_ach_billing_options; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_ach_billing_options (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    billing_option_id integer NOT NULL,
    billing_option_description character varying(50) NOT NULL
);


ALTER TABLE public.merchant_ach_billing_options OWNER TO axia;

--
-- Name: merchant_acquirers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_acquirers (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    acquirer_id integer NOT NULL,
    acquirer character varying(21) NOT NULL
);


ALTER TABLE public.merchant_acquirers OWNER TO axia;

--
-- Name: merchant_banks; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_banks (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.merchant_banks OWNER TO axia;

--
-- Name: merchant_bins; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_bins (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    bin_id integer NOT NULL,
    bin character varying(21) NOT NULL
);


ALTER TABLE public.merchant_bins OWNER TO axia;

--
-- Name: merchant_cancellation_subreasons; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_cancellation_subreasons (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(65) NOT NULL,
    visible boolean DEFAULT true NOT NULL
);


ALTER TABLE public.merchant_cancellation_subreasons OWNER TO axia;

--
-- Name: merchant_card_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_card_types (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    card_type character varying(10) NOT NULL
);


ALTER TABLE public.merchant_card_types OWNER TO axia;

--
-- Name: merchant_changes; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_changes (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.merchant_changes OWNER TO axia;

--
-- Name: merchant_notes; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_notes (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.merchant_notes OWNER TO axia;

--
-- Name: merchant_owners; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_owners (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    owner_id integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    owner_social_sec_no character varying(255),
    owner_equity integer,
    owner_name character varying(100),
    owner_title character varying(40),
    owner_social_sec_no_disp character varying(4)
);


ALTER TABLE public.merchant_owners OWNER TO axia;

--
-- Name: merchant_references; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_references (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_ref_seq_number integer NOT NULL,
    merchant_id character varying(50) NOT NULL,
    merchant_ref_type character varying(10) NOT NULL,
    bank_name character varying(100),
    person_name character varying(100),
    phone character varying(20)
);


ALTER TABLE public.merchant_references OWNER TO axia;

--
-- Name: merchant_reference_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reference_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_ref_type character varying(10) NOT NULL,
    merchant_ref_type_desc character varying(50) NOT NULL
);


ALTER TABLE public.merchant_reference_types OWNER TO axia;

--
-- Name: merchant_rejects; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_rejects (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.merchant_rejects OWNER TO axia;

--
-- Name: merchant_reject_lines; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_lines (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    rejectid integer NOT NULL,
    fee real,
    statusid integer NOT NULL,
    status_date date,
    notes text
);


ALTER TABLE public.merchant_reject_lines OWNER TO axia;

--
-- Name: merchant_reject_recurrances; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_recurrances (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(57) NOT NULL
);


ALTER TABLE public.merchant_reject_recurrances OWNER TO axia;

--
-- Name: merchant_reject_statuses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_statuses (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(57) NOT NULL,
    collected boolean,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.merchant_reject_statuses OWNER TO axia;

--
-- Name: merchant_reject_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_reject_types (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(57) NOT NULL
);


ALTER TABLE public.merchant_reject_types OWNER TO axia;

--
-- Name: merchant_uws; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_uws (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.merchant_uws OWNER TO axia;

--
-- Name: merchant_uw_final_approveds; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_uw_final_approveds (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(21) NOT NULL
);


ALTER TABLE public.merchant_uw_final_approveds OWNER TO axia;

--
-- Name: merchant_uw_final_statuses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE merchant_uw_final_statuses (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(21) NOT NULL
);


ALTER TABLE public.merchant_uw_final_statuses OWNER TO axia;

--
-- Name: networks; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE networks (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    network_id integer NOT NULL,
    network_description character varying(50)
);


ALTER TABLE public.networks OWNER TO axia;

--
-- Name: non_compliant_merchants; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW non_compliant_merchants AS
    SELECT se.id, se.merchant_id, se.merchant_email, se.merchant_name, se.password, se.email_sent, se.saq_type, se.first_scan_date, se.first_questionnaire_date, se.scan_status, se.questionnaire_status, se.pci_compliance, se.creation_date, se.sua, se.saq_completed_date, se.billing_date, se.next_billing_date, se.quarterly_scan_fee, se.compliance_fee, se.insurance_fee FROM (saq_eligibles se LEFT JOIN compliant_merchants cm ON (((se.merchant_id)::text = (cm.merchant_id)::text))) WHERE (cm.merchant_id IS NULL);


ALTER TABLE public.non_compliant_merchants OWNER TO axia;

--
-- Name: non_compliant_merchants_cs; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW non_compliant_merchants_cs AS
    SELECT se.id, se.merchant_id, se.merchant_email, se.merchant_name, se.password, se.email_sent, se.saq_type, se.first_scan_date, se.first_questionnaire_date, se.scan_status, se.questionnaire_status, se.pci_compliance, se.creation_date, se.sua, se.saq_completed_date, se.billing_date, se.next_billing_date, se.quarterly_scan_fee, se.compliance_fee, se.insurance_fee FROM saq_eligibles se WHERE ((se.pci_compliance)::text = 'No'::text);


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
-- Name: note_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE note_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    note_type character varying(10) NOT NULL,
    note_type_description character varying(50)
);


ALTER TABLE public.note_types OWNER TO axia;

--
-- Name: onlineapp_apips; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_apips (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    user_id integer,
    ip_address inet
);


ALTER TABLE public.onlineapp_apips OWNER TO axia;

--
-- Name: onlineapp_applications; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_applications (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    user_id integer,
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
    corp_contact_name_title character varying(20),
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
    term1_type character varying(20),
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
    term2_type character varying(20),
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
    install_var_rs_document_guid character varying(32)
);


ALTER TABLE public.onlineapp_applications OWNER TO axia;

--
-- Name: onlineapp_email_timeline_subjects; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_email_timeline_subjects (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    subject character varying(40) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_email_timeline_subjects OWNER TO axia;

--
-- Name: onlineapp_email_timelines; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_email_timelines (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    onlineapp_application_id integer,
    date timestamp without time zone,
    subject_id integer,
    recipient character varying(50)
);


ALTER TABLE public.onlineapp_email_timelines OWNER TO axia;

--
-- Name: onlineapp_epayments; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_epayments (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    pin integer NOT NULL,
    application_id integer NOT NULL,
    merchant_id character varying(40),
    user_id integer,
    onlineapp_applications_id integer,
    date_boarded timestamp without time zone NOT NULL,
    date_retrieved timestamp without time zone
);


ALTER TABLE public.onlineapp_epayments OWNER TO axia;

--
-- Name: onlineapp_groups; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_groups (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(100) NOT NULL,
    created timestamp(6) without time zone,
    modified timestamp(6) without time zone
);


ALTER TABLE public.onlineapp_groups OWNER TO axia;

--
-- Name: onlineapp_settings; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_settings (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    key character varying(255) NOT NULL,
    value character varying(255),
    description text
);


ALTER TABLE public.onlineapp_settings OWNER TO axia;

--
-- Name: onlineapp_users; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE onlineapp_users (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    email character varying(255) NOT NULL,
    password character varying(40) NOT NULL,
    group_id integer NOT NULL,
    created timestamp(6) without time zone NOT NULL,
    modified timestamp(6) without time zone NOT NULL,
    token character(40) DEFAULT NULL::bpchar,
    token_used timestamp without time zone,
    token_uses integer DEFAULT 0 NOT NULL,
    firstname character(40),
    lastname character(40),
    extension integer
);


ALTER TABLE public.onlineapp_users OWNER TO axia;

--
-- Name: orderitem_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orderitem_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    orderitem_type_id integer NOT NULL,
    orderitem_type_description character varying(55)
);


ALTER TABLE public.orderitem_types OWNER TO axia;

--
-- Name: orderitems; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orderitems (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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
-- Name: orderitems_replacements; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orderitems_replacements (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.orderitems_replacements OWNER TO axia;

--
-- Name: orders; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE orders (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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
-- Name: partners; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE partners (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    partner_id integer NOT NULL,
    partner_name character varying(255) NOT NULL,
    active integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.partners OWNER TO axia;

--
-- Name: pci_billings; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_billings (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    pci_billing_type_id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    billing_date date
);


ALTER TABLE public.pci_billings OWNER TO axia;

--
-- Name: pci_billing_histories; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_billing_histories (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    pci_billing_type_id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    billing_date date,
    date_change date NOT NULL,
    operation character varying(25) NOT NULL
);


ALTER TABLE public.pci_billing_histories OWNER TO axia;

--
-- Name: pci_billing_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_billing_types (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(255)
);


ALTER TABLE public.pci_billing_types OWNER TO axia;

--
-- Name: pci_compliances; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_compliances (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    pci_compliance_date_type_id integer NOT NULL,
    saq_merchant_id integer NOT NULL,
    date_complete date NOT NULL
);


ALTER TABLE public.pci_compliances OWNER TO axia;

--
-- Name: pci_compliance_date_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_compliance_date_types (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(255)
);


ALTER TABLE public.pci_compliance_date_types OWNER TO axia;

--
-- Name: pci_compliance_status_logs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pci_compliance_status_logs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    pci_compliance_date_type_id integer,
    saq_merchant_id integer,
    date_complete date,
    date_change timestamp without time zone,
    operation character varying(25)
);


ALTER TABLE public.pci_compliance_status_logs OWNER TO axia;

--
-- Name: pci_reminders; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW pci_reminders AS
    SELECT sm.id, sm.merchant_id, (SELECT (max(pc.date_complete) + '1 year'::interval) FROM pci_compliances pc WHERE ((pc.saq_merchant_id = sm.id) AND (pc.pci_compliance_date_type_id = ANY (ARRAY[1, 2, 4])))) AS saq_deadline, (SELECT (max(pc.date_complete) + '3 mons'::interval) FROM pci_compliances pc WHERE ((pc.saq_merchant_id = sm.id) AND (pc.pci_compliance_date_type_id = ANY (ARRAY[3, 5])))) AS scan_deadline, (SELECT CASE WHEN ((max(pc.date_complete) + '1 year'::interval) < now()) THEN 'SAQ EXPIRED'::text WHEN ((max(pc.date_complete) + '1 year'::interval) < (now() + '1 mon'::interval)) THEN 'SEND SAQ REMINDER'::text ELSE (date_part('day'::text, (((max(pc.date_complete) + '1 year'::interval))::timestamp with time zone - now())) || ' Days Left'::text) END AS "case" FROM pci_compliances pc WHERE ((pc.saq_merchant_id = sm.id) AND (pc.pci_compliance_date_type_id = ANY (ARRAY[1, 2, 4])))) AS saq_status, (SELECT CASE WHEN ((max(pc.date_complete) + '3 mons'::interval) < now()) THEN 'SCAN EXPIRED'::text WHEN ((max(pc.date_complete) + '3 mons'::interval) < (now() + '20 days'::interval)) THEN 'SEND SCAN REMINDER'::text ELSE (date_part('day'::text, (((max(pc.date_complete) + '3 mons'::interval))::timestamp with time zone - now())) || ' Days Left'::text) END AS "case" FROM pci_compliances pc WHERE ((pc.saq_merchant_id = sm.id) AND (pc.pci_compliance_date_type_id = ANY (ARRAY[3, 5])))) AS scan_status, (SELECT pc.pci_compliance_date_type_id FROM pci_compliances pc WHERE ((pc.pci_compliance_date_type_id = ANY (ARRAY[1, 2, 4])) AND (pc.saq_merchant_id = sm.id)) ORDER BY pc.date_complete DESC LIMIT 1) AS saq_date_type_id, (SELECT pc.pci_compliance_date_type_id FROM pci_compliances pc WHERE ((pc.pci_compliance_date_type_id = ANY (ARRAY[3, 5])) AND (pc.saq_merchant_id = sm.id)) ORDER BY pc.date_complete DESC LIMIT 1) AS scan_date_type_id FROM ((saq_merchants sm LEFT JOIN pci_compliances pc ON ((sm.id = pc.saq_merchant_id))) JOIN pci_compliance_date_types pcdt ON ((pc.pci_compliance_date_type_id = pcdt.id))) GROUP BY sm.id, sm.merchant_id ORDER BY sm.id DESC;


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
-- Name: permissions; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE permissions (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    permission_id integer NOT NULL,
    permission_group character varying(20) NOT NULL,
    permission_description character varying(100)
);


ALTER TABLE public.permissions OWNER TO axia;

--
-- Name: permission_groups; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE permission_groups (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    permission_group character varying(20) NOT NULL,
    permission_group_description character varying(50)
);


ALTER TABLE public.permission_groups OWNER TO axia;

--
-- Name: pricing_matrixes; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE pricing_matrixes (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.pricing_matrixes OWNER TO axia;

--
-- Name: products_and_services; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE products_and_services (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    products_services_type character varying(10) NOT NULL
);


ALTER TABLE public.products_and_services OWNER TO axia;

--
-- Name: products_services_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE products_services_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    products_services_type character varying(10) NOT NULL,
    products_services_description character varying(50)
);


ALTER TABLE public.products_services_types OWNER TO axia;

--
-- Name: referer_products_services_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE referer_products_services_xrefs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    ref_seq_number integer NOT NULL,
    products_services_type character varying(10) NOT NULL
);


ALTER TABLE public.referer_products_services_xrefs OWNER TO axia;

--
-- Name: referers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE referers (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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
-- Name: referers_bets; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE referers_bets (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    referers_bet_id integer NOT NULL,
    ref_seq_number integer NOT NULL,
    bet_code character varying(50) NOT NULL,
    pct real DEFAULT 0 NOT NULL
);


ALTER TABLE public.referers_bets OWNER TO axia;

--
-- Name: saq_merchant_pci_email_sents; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchant_pci_email_sents (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    saq_merchant_id integer NOT NULL,
    saq_merchant_pci_email_id integer NOT NULL,
    date_sent timestamp without time zone NOT NULL
);


ALTER TABLE public.saq_merchant_pci_email_sents OWNER TO axia;

--
-- Name: reminders; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW reminders AS
    SELECT DISTINCT saq_merchant_pci_email_sents.saq_merchant_id, count(saq_merchant_pci_email_sents.saq_merchant_id) AS count FROM saq_merchant_pci_email_sents WHERE (saq_merchant_pci_email_sents.date_sent < '2011-09-01 00:00:00'::timestamp without time zone) GROUP BY saq_merchant_pci_email_sents.saq_merchant_id HAVING (count(saq_merchant_pci_email_sents.saq_merchant_id) < 2);


ALTER TABLE public.reminders OWNER TO axia;

--
-- Name: rep_cost_structures; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE rep_cost_structures (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.rep_cost_structures OWNER TO axia;

--
-- Name: rep_partner_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE rep_partner_xrefs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    user_id integer NOT NULL,
    partner_id integer NOT NULL,
    mgr1_id integer,
    mgr2_id integer,
    profit_pct real,
    multiple integer,
    mgr1_profit_pct real,
    mgr2_profit_pct real
);


ALTER TABLE public.rep_partner_xrefs OWNER TO axia;

--
-- Name: residual_pricings; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE residual_pricings (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.residual_pricings OWNER TO axia;

--
-- Name: residual_reports; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE residual_reports (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.residual_reports OWNER TO axia;

--
-- Name: sales_goals; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE sales_goals (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    user_id integer NOT NULL,
    goal_accounts double precision,
    goal_volume double precision,
    goal_profits double precision,
    goal_statements double precision,
    goal_calls double precision,
    goal_month integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.sales_goals OWNER TO axia;

--
-- Name: sales_goal_archives; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE sales_goal_archives (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.sales_goal_archives OWNER TO axia;

--
-- Name: saq_answers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_answers (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    saq_merchant_survey_xref_id integer NOT NULL,
    saq_survey_question_xref_id integer NOT NULL,
    answer boolean NOT NULL,
    date timestamp without time zone NOT NULL
);


ALTER TABLE public.saq_answers OWNER TO axia;

--
-- Name: saq_control_scan_unboardeds; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_control_scan_unboardeds (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50),
    date_unboarded date
);


ALTER TABLE public.saq_control_scan_unboardeds OWNER TO axia;

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
-- Name: saq_eligible_wrongs; Type: VIEW; Schema: public; Owner: axia
--

CREATE VIEW saq_eligible_wrongs AS
    SELECT sm.id, sm.merchant_id, sm.merchant_email, sm.merchant_name, sm.password, sm.email_sent, scs.saq_type, scs.first_scan_date, scs.first_questionnaire_date, scs.scan_status, scs.questionnaire_status, scs.pci_compliance, scs.creation_date, scs.sua, mp.saq_completed_date, sm.billing_date, sm.next_billing_date, scs.quarterly_scan_fee, mp.compliance_fee, mp.insurance_fee FROM (((((((saq_merchants sm LEFT JOIN merchants m ON ((((sm.merchant_id)::text = (m.merchant_id)::text) AND (m.active = 1)))) LEFT JOIN merchant_pcis mp ON (((sm.merchant_id)::text = (mp.merchant_id)::text))) LEFT JOIN saq_control_scans scs ON ((substr((sm.merchant_id)::text, 5, 12) = substr((scs.merchant_id)::text, 5, 12)))) LEFT JOIN merchant_cancellations mc ON ((((sm.merchant_id)::text = (mc.merchant_id)::text) AND (mc.date_completed IS NOT NULL)))) LEFT JOIN timeline_entries tes ON ((((sm.merchant_id)::text = (tes.merchant_id)::text) AND (((tes.timeline_item)::text = 'SUB'::text) AND (tes.timeline_date_completed < '2011-08-01'::date))))) LEFT JOIN timeline_entries tei ON ((((sm.merchant_id)::text = (tei.merchant_id)::text) AND ((tei.timeline_item)::text = 'INS'::text)))) LEFT JOIN bankcards b ON (((sm.merchant_id)::text = (b.merchant_id)::text))) WHERE ((((((sm.merchant_email)::text ~~ '%@%'::text) AND (m.merchant_id IS NOT NULL)) AND ((tei.merchant_id IS NOT NULL) OR (tes.merchant_id IS NOT NULL))) AND (b.merchant_id IS NOT NULL)) AND (mc.merchant_id IS NULL));


ALTER TABLE public.saq_eligible_wrongs OWNER TO axia;

--
-- Name: saq_merchant_pci_emails; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_merchant_pci_emails (	
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    priority integer NOT NULL,
    "interval" integer NOT NULL,
    title character varying(255) NOT NULL,
    filename_prefix character varying(255) NOT NULL,
    visible boolean DEFAULT true NOT NULL
);


ALTER TABLE public.saq_merchant_pci_emails OWNER TO axia;

--
-- Name: saq_prequalifications; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_prequalifications (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    saq_merchant_id integer NOT NULL,
    result character varying(4) NOT NULL,
    date_completed timestamp without time zone NOT NULL,
    control_scan_code integer,
    control_scan_message text
);


ALTER TABLE public.saq_prequalifications OWNER TO axia;

--
-- Name: saq_questions; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_questions (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    question text NOT NULL
);


ALTER TABLE public.saq_questions OWNER TO axia;

--
-- Name: saq_survey_question_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE saq_survey_question_xrefs (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    saq_survey_id integer NOT NULL,
    saq_question_id integer NOT NULL,
    priority integer NOT NULL
);


ALTER TABLE public.saq_survey_question_xrefs OWNER TO axia;

--
-- Name: shipping_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE shipping_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    shipping_type integer NOT NULL,
    shipping_type_description character varying(50) NOT NULL
);


ALTER TABLE public.shipping_types OWNER TO axia;

--
-- Name: shipping_type_items; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE shipping_type_items (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    shipping_type integer NOT NULL,
    shipping_type_description character varying(50) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.shipping_type_items OWNER TO axia;

--
-- Name: system_transactions; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE system_transactions (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public.system_transactions OWNER TO axia;

--
-- Name: tgates; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE tgates (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    tg_rate real,
    tg_per_item real,
    tg_statement real
);


ALTER TABLE public.tgates OWNER TO axia;

--
-- Name: timeline_items; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE timeline_items (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    timeline_item character varying(50) NOT NULL,
    timeline_item_description character varying(100)
);


ALTER TABLE public.timeline_items OWNER TO axia;

--
-- Name: transaction_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE transaction_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    transaction_type character varying(10) NOT NULL,
    transaction_type_description character varying(50)
);


ALTER TABLE public.transaction_types OWNER TO axia;

--
-- Name: usaepay_rep_gtwy_add_costs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE usaepay_rep_gtwy_add_costs (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(255) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.usaepay_rep_gtwy_add_costs OWNER TO axia;

--
-- Name: usaepay_rep_gtwy_costs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE usaepay_rep_gtwy_costs (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(255) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.usaepay_rep_gtwy_costs OWNER TO axia;

--
-- Name: users; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE "users" (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
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


ALTER TABLE public."users" OWNER TO axia;

--
-- Name: user_bet_tables; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE user_bet_tables (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    bet_code character varying(50) NOT NULL,
    user_id integer NOT NULL,
    network character varying(20) NOT NULL,
    rate real,
    pi real
);


ALTER TABLE public.user_bet_tables OWNER TO axia;

--
-- Name: user_permissions; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE user_permissions (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    user_id integer NOT NULL,
    permission_id integer NOT NULL
);


ALTER TABLE public.user_permissions OWNER TO axia;

--
-- Name: user_types; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE user_types (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    user_type character varying(10) NOT NULL,
    user_type_description character varying(50)
);


ALTER TABLE public.user_types OWNER TO axia;

--
-- Name: uw_approvalinfos; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_approvalinfos (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    verified_type uw_verified_type NOT NULL
);


ALTER TABLE public.uw_approvalinfos OWNER TO axia;

--
-- Name: uw_approvalinfo_merchant_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_approvalinfo_merchant_xrefs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(255) NOT NULL,
    approvalinfo_id integer NOT NULL,
    verified_option_id integer NOT NULL,
    notes text
);


ALTER TABLE public.uw_approvalinfo_merchant_xrefs OWNER TO axia;

--
-- Name: uw_infodocs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_infodocs (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    required boolean NOT NULL
);


ALTER TABLE public.uw_infodocs OWNER TO axia;

--
-- Name: uw_infodoc_merchant_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_infodoc_merchant_xrefs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(255) NOT NULL,
    infodoc_id integer NOT NULL,
    received_id integer NOT NULL,
    notes text
);


ALTER TABLE public.uw_infodoc_merchant_xrefs OWNER TO axia;

--
-- Name: uw_receiveds; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_receiveds (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(11) NOT NULL
);


ALTER TABLE public.uw_receiveds OWNER TO axia;

--
-- Name: uw_statuses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_statuses (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.uw_statuses OWNER TO axia;

--
-- Name: uw_status_merchant_xrefs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_status_merchant_xrefs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(255) NOT NULL,
    status_id integer NOT NULL,
    datetime timestamp without time zone NOT NULL,
    notes text
);


ALTER TABLE public.uw_status_merchant_xrefs OWNER TO axia;

--
-- Name: uw_verified_options; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE uw_verified_options (
    id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    name character varying(37) NOT NULL,
    verified_type uw_verified_type NOT NULL
);


ALTER TABLE public.uw_verified_options OWNER TO axia;

--
-- Name: vendors; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE vendors (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    vendor_id integer NOT NULL,
    vendor_description character varying(50) NOT NULL,
    rank integer
);


ALTER TABLE public.vendors OWNER TO axia;

--
-- Name: virtual_checks; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE virtual_checks (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    vc_mid character varying(20),
    vc_web_based_rate real,
    vc_web_based_pi real,
    vc_monthly_fee real,
    vc_gateway_fee real
);


ALTER TABLE public.virtual_checks OWNER TO axia;

--
-- Name: virtual_check_webs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE virtual_check_webs (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    vcweb_mid character varying(20),
    vcweb_web_based_rate real,
    vcweb_web_based_pi real,
    vcweb_monthly_fee real,
    vcweb_gateway_fee real
);


ALTER TABLE public.virtual_check_webs OWNER TO axia;

--
-- Name: warranties; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE warranties (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    warranty integer NOT NULL,
    warranty_description character varying(50) NOT NULL,
    cost real NOT NULL
);


ALTER TABLE public.warranties OWNER TO axia;

--
-- Name: webpasses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

CREATE TABLE webpasses (
	id varchar(36) PRIMARY KEY default uuid_generate_v4(),
    merchant_id character varying(50) NOT NULL,
    wp_rate real,
    wp_per_item real,
    wp_statement real
);


ALTER TABLE public.webpasses OWNER TO axia;

--
-- Name: ach_merchantid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ach_merchantid ON achs USING btree (merchant_id);


--
-- Name: ach_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ach_mid ON achs USING btree (ach_mid);


--
-- Name: address_merchant_type_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX address_merchant_type_idx ON addresses USING btree (address_type, merchant_id);


--
-- Name: address_owner_uidx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX address_owner_uidx ON addresses USING btree (owner_id);


--
-- Name: adjustments_t_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX adjustments_t_user_idx ON adjustments_ts USING btree (user_id);


--
-- Name: adjustments_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX adjustments_user_idx ON adjustments USING btree (user_id);


--
-- Name: admin_entity_view_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX admin_entity_view_user_idx ON admin_entity_views USING btree (user_id);


--
-- Name: bankcard_bcmid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_bcmid ON bankcards USING btree (bc_mid);


--
-- Name: bankcard_bet; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_bet ON bankcards USING btree (bet_code);


--
-- Name: bankcard_urgac_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_urgac_index ON bankcards USING btree (bc_usaepay_rep_gtwy_add_cost_id);


--
-- Name: bankcard_urgc_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX bankcard_urgc_index ON bankcards USING btree (bc_usaepay_rep_gtwy_cost_id);


--
-- Name: commission_pricing_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index1 ON commission_pricings USING btree (merchant_id);


--
-- Name: commission_pricing_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index2 ON commission_pricings USING btree (user_id);


--
-- Name: commission_pricing_index3; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index3 ON commission_pricings USING btree (c_month);


--
-- Name: commission_pricing_index4; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_index4 ON commission_pricings USING btree (c_year);


--
-- Name: commission_pricing_products_services_type; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX commission_pricing_products_services_type ON commission_pricings USING btree (products_services_type);


--
-- Name: cr_mid_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_mid_idx ON commission_report_olds USING btree (merchant_id);


--
-- Name: cr_partner_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX cr_partner_id_index ON commission_reports USING btree (partner_id);


--
-- Name: crnew_mid_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX crnew_mid_idx ON commission_reports USING btree (merchant_id);


--
-- Name: discover_bet_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX discover_bet_index ON discovers USING btree (bet_code);


--
-- Name: discover_user_bet_table_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX discover_user_bet_table_index1 ON discover_user_bet_tables USING btree (user_id);


--
-- Name: eptx_programming_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX eptx_programming_idx ON equipment_programming_type_xrefs USING btree (programming_id);


--
-- Name: equipment_item_active; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_item_active ON equipment_items USING btree (active);


--
-- Name: equipment_item_type; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_item_type ON equipment_items USING btree (equipment_type);


--
-- Name: equipment_item_warranty; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_item_warranty ON equipment_items USING btree (warranty);


--
-- Name: equipment_programming_appid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_appid ON equipment_programmings USING btree (app_id);


--
-- Name: equipment_programming_merch_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_merch_idx ON equipment_programmings USING btree (merchant_id);


--
-- Name: equipment_programming_serialnum; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_serialnum ON equipment_programmings USING btree (serial_number);


--
-- Name: equipment_programming_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_status ON equipment_programmings USING btree (status);


--
-- Name: equipment_programming_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX equipment_programming_userid ON equipment_programmings USING btree (user_id);


--
-- Name: gateway_gw_usaepay_add_cost; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gateway_gw_usaepay_add_cost ON gateway0s USING btree (gw_usaepay_rep_gtwy_add_cost_id);


--
-- Name: gateway_gw_usaepay_cost; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gateway_gw_usaepay_cost ON gateway0s USING btree (gw_usaepay_rep_gtwy_cost_id);


--
-- Name: gw1_gatewayid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gw1_gatewayid ON gateway1s USING btree (gw1_gateway_id);


--
-- Name: gw2_gatewayid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX gw2_gatewayid ON gateway2 USING btree (gw2_gateway_id);


--
-- Name: mct_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mct_merchant_idx ON merchant_card_types USING btree (merchant_id);


--
-- Name: merchant_ach_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_ach_idx ON merchant_achs USING btree (merchant_id);


--
-- Name: merchant_active; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_active ON merchants USING btree (active);


--
-- Name: merchant_cancellation_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index1 ON merchant_cancellations USING btree (date_submitted);


--
-- Name: merchant_cancellation_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index2 ON merchant_cancellations USING btree (date_completed);


--
-- Name: merchant_cancellation_index3; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index3 ON merchant_cancellations USING btree (status);


--
-- Name: merchant_cancellation_index9; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index9 ON merchant_cancellations USING btree (date_inactive);


--
-- Name: merchant_cancellation_index99; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_cancellation_index99 ON merchant_cancellations USING btree (subreason_id);


--
-- Name: merchant_change_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_change_merchant_idx ON merchant_changes USING btree (merchant_id);


--
-- Name: merchant_dba_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_dba_idx ON merchants USING btree (merchant_dba);


--
-- Name: merchant_dbalower_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_dbalower_idx ON merchants USING btree (merchant_dba);


--
-- Name: merchant_entity; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_entity ON merchants USING btree (entity);


--
-- Name: merchant_groupid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_groupid ON merchants USING btree (group_id);


--
-- Name: merchant_netid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_netid ON merchants USING btree (network_id);


--
-- Name: merchant_note_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_note_merchant_idx ON merchant_notes USING btree (merchant_id);


--
-- Name: merchant_owner_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_owner_merchant_idx ON merchant_owners USING btree (merchant_id);


--
-- Name: merchant_reference_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_reference_mid ON merchant_references USING btree (merchant_id);


--
-- Name: merchant_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_userid ON merchants USING btree (user_id);


--
-- Name: merchant_uw_expedited_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_expedited_index ON merchant_uws USING btree (expedited);


--
-- Name: merchant_uw_final_approved_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_final_approved_id_index ON merchant_uws USING btree (final_approved_id);


--
-- Name: merchant_uw_final_date_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_final_date_index ON merchant_uws USING btree (final_date);


--
-- Name: merchant_uw_final_status_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_final_status_id_index ON merchant_uws USING btree (final_status_id);


--
-- Name: merchant_uw_tier_assignment_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX merchant_uw_tier_assignment_index ON merchant_uws USING btree (tier_assignment);


--
-- Name: mp_compliance_fee; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mp_compliance_fee ON merchant_pcis USING btree (compliance_fee);


--
-- Name: mp_insurance_fee; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mp_insurance_fee ON merchant_pcis USING btree (insurance_fee);


--
-- Name: mp_saq_completed_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mp_saq_completed_date ON merchant_pcis USING btree (saq_completed_date);


--
-- Name: mpid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mpid_index1 ON merchants USING btree (partner_id);


--
-- Name: mr_merchant_id_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_merchant_id_index1 ON merchant_rejects USING btree (merchant_id);


--
-- Name: mr_open_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_open_index1 ON merchant_rejects USING btree (open);


--
-- Name: mr_recurranceid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_recurranceid_index1 ON merchant_rejects USING btree (recurranceid);


--
-- Name: mr_reject_date_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_reject_date_index1 ON merchant_rejects USING btree (reject_date);


--
-- Name: mr_status_date_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_status_date_index1 ON merchant_reject_lines USING btree (status_date);


--
-- Name: mr_trace_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX mr_trace_index1 ON merchant_rejects USING btree (trace);


--
-- Name: mr_typeid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mr_typeid_index1 ON merchant_rejects USING btree (typeid);


--
-- Name: mrl_rejectid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mrl_rejectid_index1 ON merchant_reject_lines USING btree (rejectid);


--
-- Name: mrl_statusid_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mrl_statusid_index1 ON merchant_reject_lines USING btree (statusid);


--
-- Name: mrs_collected_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX mrs_collected_index1 ON merchant_reject_statuses USING btree (collected);


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

CREATE INDEX orderitems_replacement_oid ON orderitems_replacements USING btree (orderitem_id);


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
-- Name: p_and_s_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX p_and_s_merchant_idx ON products_and_services USING btree (merchant_id);


--
-- Name: pactive_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pactive_index1 ON partners USING btree (active);


--
-- Name: pricing_matrix_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pricing_matrix_user_idx ON pricing_matrixes USING btree (user_id);


--
-- Name: pricing_matrix_usertype; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX pricing_matrix_usertype ON pricing_matrixes USING btree (user_type);


--
-- Name: referers_active; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX referers_active ON referers USING btree (active);


--
-- Name: referers_bet_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX referers_bet_index1 ON referers_bets USING btree (ref_seq_number);


--
-- Name: referers_bet_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX referers_bet_index2 ON referers_bets USING btree (bet_code);


--
-- Name: residual_pricing_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_mid ON residual_pricings USING btree (merchant_id);


--
-- Name: residual_pricing_month; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_month ON residual_pricings USING btree (r_month);


--
-- Name: residual_pricing_network; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_network ON residual_pricings USING btree (r_network);


--
-- Name: residual_pricing_pst; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_pst ON residual_pricings USING btree (products_services_type);


--
-- Name: residual_pricing_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_userid ON residual_pricings USING btree (user_id);


--
-- Name: residual_pricing_year; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_pricing_year ON residual_pricings USING btree (r_year);


--
-- Name: residual_report_merchantid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_merchantid ON residual_reports USING btree (merchant_id);


--
-- Name: residual_report_month; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_month ON residual_reports USING btree (r_month);


--
-- Name: residual_report_pst; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_pst ON residual_reports USING btree (products_services_type);


--
-- Name: residual_report_seqnum; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_seqnum ON residual_reports USING btree (ref_seq_number);


--
-- Name: residual_report_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_status ON residual_reports USING btree (status);


--
-- Name: residual_report_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_userid ON residual_reports USING btree (user_id);


--
-- Name: residual_report_year; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX residual_report_year ON residual_reports USING btree (r_year);


--
-- Name: rpxref_index1; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rpxref_index1 ON rep_partner_xrefs USING btree (mgr1_id);


--
-- Name: rpxref_index2; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rpxref_index2 ON rep_partner_xrefs USING btree (mgr2_id);


--
-- Name: rr_manager_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rr_manager_id ON residual_reports USING btree (manager_id);


--
-- Name: rr_manager_id_secondary; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rr_manager_id_secondary ON residual_reports USING btree (manager_id_secondary);


--
-- Name: rr_partner_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX rr_partner_id_index ON residual_reports USING btree (partner_id);


--
-- Name: sa_saq_merchant_survey_xref_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sa_saq_merchant_survey_xref_id ON saq_answers USING btree (saq_merchant_survey_xref_id);


--
-- Name: sa_saq_survey_question_xref_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sa_saq_survey_question_xref_id ON saq_answers USING btree (saq_survey_question_xref_id);


--
-- Name: sales_goal_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sales_goal_userid ON sales_goals USING btree (user_id);


--
-- Name: saw_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX saw_merchant_idx ON saq_merchants USING btree (merchant_id);


--
-- Name: scs_creation_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_creation_date ON saq_control_scans USING btree (creation_date);


--
-- Name: scs_first_questionnaire_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_first_questionnaire_date ON saq_control_scans USING btree (first_questionnaire_date);


--
-- Name: scs_first_scan_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_first_scan_date ON saq_control_scans USING btree (first_scan_date);


--
-- Name: scs_pci_compliance; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_pci_compliance ON saq_control_scans USING btree (pci_compliance);


--
-- Name: scs_quarterly_scan_fee; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_quarterly_scan_fee ON saq_control_scans USING btree (quarterly_scan_fee);


--
-- Name: scs_questionnaire_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_questionnaire_status ON saq_control_scans USING btree (questionnaire_status);


--
-- Name: scs_saq_type; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_saq_type ON saq_control_scans USING btree (saq_type);


--
-- Name: scs_scan_status; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_scan_status ON saq_control_scans USING btree (scan_status);


--
-- Name: scs_sua; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scs_sua ON saq_control_scans USING btree (sua);


--
-- Name: scsu_date_unboarded; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX scsu_date_unboarded ON saq_control_scan_unboardeds USING btree (date_unboarded);


--
-- Name: sg_archive_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX sg_archive_user_idx ON sales_goal_archives USING btree (user_id, goal_month, goal_year);


--
-- Name: sm_billing_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_billing_date ON saq_merchants USING btree (billing_date);


--
-- Name: sm_email_sent; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_email_sent ON saq_merchants USING btree (email_sent);


--
-- Name: sm_merchant_email; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_merchant_email ON saq_merchants USING btree (merchant_email);


--
-- Name: sm_merchant_name; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_merchant_name ON saq_merchants USING btree (merchant_name);


--
-- Name: sm_next_billing_date; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_next_billing_date ON saq_merchants USING btree (next_billing_date);


--
-- Name: sm_password; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sm_password ON saq_merchants USING btree (password);


--
-- Name: smpes_date_sent; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smpes_date_sent ON saq_merchant_pci_email_sents USING btree (date_sent);


--
-- Name: smpes_saq_merchant_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smpes_saq_merchant_id ON saq_merchant_pci_email_sents USING btree (saq_merchant_id);


--
-- Name: smpes_saq_merchant_pci_email_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smpes_saq_merchant_pci_email_id ON saq_merchant_pci_email_sents USING btree (saq_merchant_pci_email_id);


--
-- Name: smsx_acknowledgement_name; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_acknowledgement_name ON saq_merchant_survey_xrefs USING btree (acknowledgement_name);


--
-- Name: smsx_datecomplete; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_datecomplete ON saq_merchant_survey_xrefs USING btree (datecomplete);


--
-- Name: smsx_saq_confirmation_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_confirmation_survey_id ON saq_merchant_survey_xrefs USING btree (saq_confirmation_survey_id);


--
-- Name: smsx_saq_eligibility_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_eligibility_survey_id ON saq_merchant_survey_xrefs USING btree (saq_eligibility_survey_id);


--
-- Name: smsx_saq_merchant_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_merchant_id ON saq_merchant_survey_xrefs USING btree (saq_merchant_id);


--
-- Name: smsx_saq_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX smsx_saq_survey_id ON saq_merchant_survey_xrefs USING btree (saq_survey_id);


--
-- Name: sp_saq_merchant_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sp_saq_merchant_id ON saq_prequalifications USING btree (saq_merchant_id);


--
-- Name: ss_confirmation_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ss_confirmation_survey_id ON saq_surveys USING btree (confirmation_survey_id);


--
-- Name: ss_eligibility_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ss_eligibility_survey_id ON saq_surveys USING btree (eligibility_survey_id);


--
-- Name: ssqx_saq_question_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ssqx_saq_question_id ON saq_survey_question_xrefs USING btree (saq_question_id);


--
-- Name: ssqx_saq_survey_id; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX ssqx_saq_survey_id ON saq_survey_question_xrefs USING btree (saq_survey_id);


--
-- Name: sys_trans_merchant_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sys_trans_merchant_idx ON system_transactions USING btree (merchant_id);


--
-- Name: sys_trans_session_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX sys_trans_session_idx ON system_transactions USING btree (session_id);


--
-- Name: system_transaction_chnageid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_chnageid ON system_transactions USING btree (change_id);


--
-- Name: system_transaction_noteid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_noteid ON system_transactions USING btree (merchant_note_id);


--
-- Name: system_transaction_orderid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_orderid ON system_transactions USING btree (order_id);


--
-- Name: system_transaction_programmingi; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_programmingi ON system_transactions USING btree (programming_id);


--
-- Name: system_transaction_userid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX system_transaction_userid ON system_transactions USING btree (user_id);


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

CREATE INDEX ubt_user_idx ON user_bet_tables USING btree (user_id);


--
-- Name: user_bet_table_pk; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE UNIQUE INDEX user_bet_table_pk ON user_bet_tables USING btree (bet_code, user_id, network);


--
-- Name: user_email_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_email_idx ON "users" USING btree (user_email);


--
-- Name: user_lastname_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_lastname_idx ON "users" USING btree (user_last_name);


--
-- Name: user_parent_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_parent_user_idx ON "users" USING btree (parent_user);


--
-- Name: user_parent_user_secondary_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_parent_user_secondary_idx ON "users" USING btree (parent_user_secondary);


--
-- Name: user_permission_user_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_permission_user_idx ON user_permissions USING btree (user_id);


--
-- Name: user_username_idx; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX user_username_idx ON "users" USING btree (username);


--
-- Name: uw_amx_verified_option_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_amx_verified_option_id_index ON uw_approvalinfo_merchant_xrefs USING btree (verified_option_id);


--
-- Name: uw_approvalinfo_priority_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_approvalinfo_priority_index ON uw_approvalinfos USING btree (priority);


--
-- Name: uw_approvalinfo_verified_type_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_approvalinfo_verified_type_index ON uw_approvalinfos USING btree (verified_type);


--
-- Name: uw_imx_received_id_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_imx_received_id_index ON uw_infodoc_merchant_xrefs USING btree (received_id);


--
-- Name: uw_infodoc_priority_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_infodoc_priority_index ON uw_infodocs USING btree (priority);


--
-- Name: uw_infodoc_required_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_infodoc_required_index ON uw_infodocs USING btree (required);


--
-- Name: uw_smx_datetime_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_smx_datetime_index ON uw_status_merchant_xrefs USING btree (datetime);


--
-- Name: uw_status_priority_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_status_priority_index ON uw_statuses USING btree (priority);


--
-- Name: uw_verified_option_verified_type_index; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX uw_verified_option_verified_type_index ON uw_verified_options USING btree (verified_type);


--
-- Name: vendor_rank; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX vendor_rank ON vendors USING btree (rank);


--
-- Name: virtual_check_mid; Type: INDEX; Schema: public; Owner: axia; Tablespace: 
--

CREATE INDEX virtual_check_mid ON virtual_checks USING btree (vc_mid);


--
-- Name: pci_billing_audit_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER pci_billing_audit_ins
    AFTER INSERT OR DELETE OR UPDATE ON pci_billings
    FOR EACH ROW
    EXECUTE PROCEDURE pci_billing_history_function();


--
-- Name: pci_compliance_status_audit_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER pci_compliance_status_audit_ins
    AFTER INSERT OR DELETE ON pci_compliances
    FOR EACH ROW
    EXECUTE PROCEDURE pci_compliance_status_log_function();


--
-- Name: perform_update_pc_3rd; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_3rd
    AFTER UPDATE ON merchant_pcis
    FOR EACH ROW
    EXECUTE PROCEDURE update_pci_compliance_3rd();


--
-- Name: perform_update_pc_ax; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_ax
    AFTER UPDATE ON saq_merchant_survey_xrefs
    FOR EACH ROW
    EXECUTE PROCEDURE update_pci_compliance_ax();


--
-- Name: perform_update_pc_cs_del; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_del
    AFTER DELETE ON saq_control_scans
    FOR EACH ROW
    EXECUTE PROCEDURE update_pci_compliance_cs_del();


--
-- Name: perform_update_pc_cs_saq_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_saq_ins
    AFTER INSERT ON saq_control_scans
    FOR EACH ROW
    EXECUTE PROCEDURE update_pci_compliance_cs_saq_ins();


--
-- Name: perform_update_pc_cs_scan_ins; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_scan_ins
    AFTER INSERT ON saq_control_scans
    FOR EACH ROW
    EXECUTE PROCEDURE update_pci_compliance_cs_scan_ins();


--
-- Name: perform_update_pc_cs_up; Type: TRIGGER; Schema: public; Owner: axia
--

CREATE TRIGGER perform_update_pc_cs_up
    AFTER UPDATE ON saq_control_scans
    FOR EACH ROW
    EXECUTE PROCEDURE update_pci_compliance_cs_up();


--
-- Name: bankcard_bc_gw_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY bankcards
    ADD CONSTRAINT bankcard_bc_gw_gateway_id_fkey FOREIGN KEY (bc_gw_gateway_id) REFERENCES gateways(id);


--
-- Name: bankcard_bc_usaepay_rep_gtwy_add_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY bankcards
    ADD CONSTRAINT bankcard_bc_usaepay_rep_gtwy_add_cost_id_fkey FOREIGN KEY (bc_usaepay_rep_gtwy_add_cost_id) REFERENCES usaepay_rep_gtwy_add_costs(id);


--
-- Name: bankcard_bc_usaepay_rep_gtwy_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY bankcards
    ADD CONSTRAINT bankcard_bc_usaepay_rep_gtwy_cost_id_fkey FOREIGN KEY (bc_usaepay_rep_gtwy_cost_id) REFERENCES usaepay_rep_gtwy_costs(id);


--
-- Name: commission_report_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY commission_reports
    ADD CONSTRAINT commission_report_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partners(partner_id);


--
-- Name: debit_acquirer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY debits
    ADD CONSTRAINT debit_acquirer_id_fkey FOREIGN KEY (acquirer_id) REFERENCES debit_acquirers(debit_acquirer_id);


--
-- Name: discover_disc_gw_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY discovers
    ADD CONSTRAINT discover_disc_gw_gateway_id_fkey FOREIGN KEY (disc_gw_gateway_id) REFERENCES gateways(id);


--
-- Name: discover_user_bet_table_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY discover_user_bet_tables
    ADD CONSTRAINT discover_user_bet_table_user_id_fkey FOREIGN KEY (user_id) REFERENCES "users"(user_id);


--
-- Name: equipment_programming_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY equipment_programmings
    ADD CONSTRAINT equipment_programming_gateway_id_fkey FOREIGN KEY (gateway_id) REFERENCES gateways(id);


--
-- Name: fk_address_address_type; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY addresses
    ADD CONSTRAINT fk_address_address_type FOREIGN KEY (address_type) REFERENCES address_types(address_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_address_owner; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY addresses
    ADD CONSTRAINT fk_address_owner FOREIGN KEY (owner_id) REFERENCES merchant_owners(owner_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_adjustments_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY adjustments
    ADD CONSTRAINT fk_adjustments_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_aev_entity; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY admin_entity_views
    ADD CONSTRAINT fk_aev_entity FOREIGN KEY (entity) REFERENCES entities(entity) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_aev_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY admin_entity_views
    ADD CONSTRAINT fk_aev_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_commission_report_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY commission_report_olds
    ADD CONSTRAINT fk_commission_report_merchant FOREIGN KEY (merchant_id_old) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_debit_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY debits
    ADD CONSTRAINT fk_debit_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_ebt_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY ebts
    ADD CONSTRAINT fk_ebt_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_eptx_prog_ig; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY equipment_programming_type_xrefs
    ADD CONSTRAINT fk_eptx_prog_ig FOREIGN KEY (programming_id) REFERENCES equipment_programmings(programming_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_equipment_item_type; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY equipment_items
    ADD CONSTRAINT fk_equipment_item_type FOREIGN KEY (equipment_type) REFERENCES equipment_types(equipment_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_gift_card_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gift_cards
    ADD CONSTRAINT fk_gift_card_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_mct_cardtype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_card_types
    ADD CONSTRAINT fk_mct_cardtype FOREIGN KEY (card_type) REFERENCES card_types(card_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_mct_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_card_types
    ADD CONSTRAINT fk_mct_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_change_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_changes
    ADD CONSTRAINT fk_merchant_change_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_entity; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT fk_merchant_entity FOREIGN KEY (entity) REFERENCES entities(entity) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_group; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT fk_merchant_group FOREIGN KEY (group_id) REFERENCES "groups"(group_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_note_notetype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_notes
    ADD CONSTRAINT fk_merchant_note_notetype FOREIGN KEY (note_type) REFERENCES note_types(note_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_note_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_notes
    ADD CONSTRAINT fk_merchant_note_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_ref; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT fk_merchant_ref FOREIGN KEY (ref_seq_number) REFERENCES referers(ref_seq_number) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_reference_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_references
    ADD CONSTRAINT fk_merchant_reference_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_merchant_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT fk_merchant_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orderitems_equipitem; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT fk_orderitems_equipitem FOREIGN KEY (equipment_item) REFERENCES equipment_items(equipment_item) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orderitems_equiptype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT fk_orderitems_equiptype FOREIGN KEY (equipment_type) REFERENCES equipment_types(equipment_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orders_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT fk_orders_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_orders_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orders
    ADD CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_p_and_s_type; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY products_and_services
    ADD CONSTRAINT fk_p_and_s_type FOREIGN KEY (products_services_type) REFERENCES products_services_types(products_services_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_permission_permgroup; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT fk_permission_permgroup FOREIGN KEY (permission_group) REFERENCES permission_groups(permission_group) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_pricing_matrix_userid; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pricing_matrixes
    ADD CONSTRAINT fk_pricing_matrix_userid FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_pricing_matrix_usertype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pricing_matrixes
    ADD CONSTRAINT fk_pricing_matrix_usertype FOREIGN KEY (user_type) REFERENCES user_types(user_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_rep_cost_structure_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_cost_structures
    ADD CONSTRAINT fk_rep_cost_structure_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_residual_report_[prodtype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_reports
    ADD CONSTRAINT "fk_residual_report_[prodtype" FOREIGN KEY (products_services_type) REFERENCES products_services_types(products_services_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_residual_report_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_reports
    ADD CONSTRAINT fk_residual_report_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_residual_report_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_reports
    ADD CONSTRAINT fk_residual_report_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_sys_trans_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY system_transactions
    ADD CONSTRAINT fk_sys_trans_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_ubt_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY user_bet_tables
    ADD CONSTRAINT fk_ubt_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_entity; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY "users"
    ADD CONSTRAINT fk_user_entity FOREIGN KEY (entity) REFERENCES entities(entity) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_permission_perm; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY user_permissions
    ADD CONSTRAINT fk_user_permission_perm FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_permission_user; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY user_permissions
    ADD CONSTRAINT fk_user_permission_user FOREIGN KEY (user_id) REFERENCES "users"(user_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_user_usertype; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY "users"
    ADD CONSTRAINT fk_user_usertype FOREIGN KEY (user_type) REFERENCES user_types(user_type) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fk_virtual_check_merchant; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY virtual_checks
    ADD CONSTRAINT fk_virtual_check_merchant FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: fkey_saq_answer_survey_question_xref_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_answers
    ADD CONSTRAINT fkey_saq_answer_survey_question_xref_id FOREIGN KEY (saq_survey_question_xref_id) REFERENCES saq_survey_question_xrefs(id);


--
-- Name: fkey_saq_merchant_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchants
    ADD CONSTRAINT fkey_saq_merchant_merchant_id FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: fkey_saq_merchant_survey_xref_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_answers
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_id FOREIGN KEY (saq_merchant_survey_xref_id) REFERENCES saq_merchant_survey_xrefs(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_confirmation_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xrefs
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_confirmation_survey_id FOREIGN KEY (saq_confirmation_survey_id) REFERENCES saq_merchant_survey_xrefs(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_eligibility_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xrefs
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_eligibility_survey_id FOREIGN KEY (saq_eligibility_survey_id) REFERENCES saq_merchant_survey_xrefs(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xrefs
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_merchant_id FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchants(id);


--
-- Name: fkey_saq_merchant_survey_xref_saq_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_survey_xrefs
    ADD CONSTRAINT fkey_saq_merchant_survey_xref_saq_survey_id FOREIGN KEY (saq_survey_id) REFERENCES saq_surveys(id);


--
-- Name: fkey_saq_prequalification_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_prequalifications
    ADD CONSTRAINT fkey_saq_prequalification_merchant_id FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchants(id);


--
-- Name: fkey_saq_survey_confirmation_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_surveys
    ADD CONSTRAINT fkey_saq_survey_confirmation_survey_id FOREIGN KEY (confirmation_survey_id) REFERENCES saq_surveys(id);


--
-- Name: fkey_saq_survey_eligibility_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_surveys
    ADD CONSTRAINT fkey_saq_survey_eligibility_survey_id FOREIGN KEY (eligibility_survey_id) REFERENCES saq_surveys(id);


--
-- Name: fkey_saq_survey_question_xref_saq_question_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_survey_question_xrefs
    ADD CONSTRAINT fkey_saq_survey_question_xref_saq_question_id FOREIGN KEY (saq_question_id) REFERENCES saq_questions(id);


--
-- Name: fkey_saq_survey_question_xref_saq_survey_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_survey_question_xrefs
    ADD CONSTRAINT fkey_saq_survey_question_xref_saq_survey_id FOREIGN KEY (saq_survey_id) REFERENCES saq_surveys(id);


--
-- Name: gateway1_gw1_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway1s
    ADD CONSTRAINT gateway1_gw1_gateway_id_fkey FOREIGN KEY (gw1_gateway_id) REFERENCES gateways(id);


--
-- Name: gateway2_gw2_gateway_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway2
    ADD CONSTRAINT gateway2_gw2_gateway_id_fkey FOREIGN KEY (gw2_gateway_id) REFERENCES gateways(id);


--
-- Name: gateway_gw_usaepay_rep_gtwy_add_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway0s
    ADD CONSTRAINT gateway_gw_usaepay_rep_gtwy_add_cost_id_fkey FOREIGN KEY (gw_usaepay_rep_gtwy_add_cost_id) REFERENCES usaepay_rep_gtwy_add_costs(id);


--
-- Name: gateway_gw_usaepay_rep_gtwy_cost_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY gateway0s
    ADD CONSTRAINT gateway_gw_usaepay_rep_gtwy_cost_id_fkey FOREIGN KEY (gw_usaepay_rep_gtwy_cost_id) REFERENCES usaepay_rep_gtwy_costs(id);


--
-- Name: merchant_acquirer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT merchant_acquirer_id_fkey FOREIGN KEY (acquirer_id) REFERENCES merchant_acquirers(acquirer_id);


--
-- Name: merchant_bin_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT merchant_bin_id_fkey FOREIGN KEY (bin_id) REFERENCES merchant_bins(bin_id);


--
-- Name: merchant_cancellation_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_cancellations
    ADD CONSTRAINT merchant_cancellation_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: merchant_cancellation_subreason_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_cancellations
    ADD CONSTRAINT merchant_cancellation_subreason_id_fkey FOREIGN KEY (subreason_id) REFERENCES merchant_cancellation_subreasons(id);


--
-- Name: merchant_onlineapp_application_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT merchant_onlineapp_application_id_fkey FOREIGN KEY (onlineapp_application_id) REFERENCES onlineapp_applications(id);


--
-- Name: merchant_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchants
    ADD CONSTRAINT merchant_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partners(partner_id);


--
-- Name: merchant_pci_merchant_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_pcis
    ADD CONSTRAINT merchant_pci_merchant_id_fk FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: merchant_reject_line_rejectid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reject_lines
    ADD CONSTRAINT merchant_reject_line_rejectid_fkey FOREIGN KEY (rejectid) REFERENCES merchant_rejects(id);


--
-- Name: merchant_reject_line_statusid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_reject_lines
    ADD CONSTRAINT merchant_reject_line_statusid_fkey FOREIGN KEY (statusid) REFERENCES merchant_reject_statuses(id);


--
-- Name: merchant_reject_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_rejects
    ADD CONSTRAINT merchant_reject_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: merchant_reject_recurranceid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_rejects
    ADD CONSTRAINT merchant_reject_recurranceid_fkey FOREIGN KEY (recurranceid) REFERENCES merchant_reject_recurrances(id);


--
-- Name: merchant_reject_typeid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_rejects
    ADD CONSTRAINT merchant_reject_typeid_fkey FOREIGN KEY (typeid) REFERENCES merchant_reject_types(id);


--
-- Name: merchant_uw_final_approved_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_uws
    ADD CONSTRAINT merchant_uw_final_approved_id_fkey FOREIGN KEY (final_approved_id) REFERENCES merchant_uw_final_approveds(id);


--
-- Name: merchant_uw_final_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_uws
    ADD CONSTRAINT merchant_uw_final_status_id_fkey FOREIGN KEY (final_status_id) REFERENCES merchant_uw_final_statuses(id);


--
-- Name: merchant_uw_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY merchant_uws
    ADD CONSTRAINT merchant_uw_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: onlineapp_apips_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_apips
    ADD CONSTRAINT onlineapp_apips_user_id_fkey FOREIGN KEY (user_id) REFERENCES onlineapp_users(id);


--
-- Name: onlineapp_email_timelines_app_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_email_timelines
    ADD CONSTRAINT onlineapp_email_timelines_app_id_fkey FOREIGN KEY (app_id) REFERENCES onlineapp_applications(id);


--
-- Name: onlineapp_email_timelines_subject_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_email_timelines
    ADD CONSTRAINT onlineapp_email_timelines_subject_id_fkey FOREIGN KEY (subject_id) REFERENCES onlineapp_email_timeline_subjects(id);


--
-- Name: onlineapp_epayments_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: onlineapp_epayments_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_user_id_fkey FOREIGN KEY (user_id) REFERENCES onlineapp_users(id);


--
-- Name: orderitems_replacement_orderitem_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems_replacements
    ADD CONSTRAINT orderitems_replacement_orderitem_id_fkey FOREIGN KEY (orderitem_id) REFERENCES orderitems(orderitem_id);


--
-- Name: orderitems_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_type_id_fkey FOREIGN KEY (type_id) REFERENCES orderitem_types(orderitem_type_id);


--
-- Name: pci_billing_pci_billing_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_billings
    ADD CONSTRAINT pci_billing_pci_billing_type_id_fkey FOREIGN KEY (pci_billing_type_id) REFERENCES pci_billing_types(id);


--
-- Name: pci_compliance_pci_compliance_date_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_compliances
    ADD CONSTRAINT pci_compliance_pci_compliance_date_type_id_fkey FOREIGN KEY (pci_compliance_date_type_id) REFERENCES pci_compliance_date_types(id);


--
-- Name: pci_compliance_saq_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY pci_compliances
    ADD CONSTRAINT pci_compliance_saq_merchant_id_fkey FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchants(id);


--
-- Name: referer_products_services_xref_products_services_type_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY referer_products_services_xrefs
    ADD CONSTRAINT referer_products_services_xref_products_services_type_fkey FOREIGN KEY (products_services_type) REFERENCES products_services_types(products_services_type);


--
-- Name: referer_products_services_xref_ref_seq_number_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY referer_products_services_xrefs
    ADD CONSTRAINT referer_products_services_xref_ref_seq_number_fkey FOREIGN KEY (ref_seq_number) REFERENCES referers(ref_seq_number);


--
-- Name: referers_bet_ref_seq_number_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY referers_bets
    ADD CONSTRAINT referers_bet_ref_seq_number_fkey FOREIGN KEY (ref_seq_number) REFERENCES referers(ref_seq_number);


--
-- Name: rep_partner_xref_mgr1_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xrefs
    ADD CONSTRAINT rep_partner_xref_mgr1_id_fkey FOREIGN KEY (mgr1_id) REFERENCES "users"(user_id);


--
-- Name: rep_partner_xref_mgr2_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xrefs
    ADD CONSTRAINT rep_partner_xref_mgr2_id_fkey FOREIGN KEY (mgr2_id) REFERENCES "users"(user_id);


--
-- Name: rep_partner_xref_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xrefs
    ADD CONSTRAINT rep_partner_xref_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partners(partner_id);


--
-- Name: rep_partner_xref_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY rep_partner_xrefs
    ADD CONSTRAINT rep_partner_xref_user_id_fkey FOREIGN KEY (user_id) REFERENCES "users"(user_id);


--
-- Name: residual_report_partner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY residual_reports
    ADD CONSTRAINT residual_report_partner_id_fkey FOREIGN KEY (partner_id) REFERENCES partners(partner_id);


--
-- Name: saq_merchant_pci_email_sent_saq_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_pci_email_sents
    ADD CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_id_fkey FOREIGN KEY (saq_merchant_id) REFERENCES saq_merchants(id);


--
-- Name: saq_merchant_pci_email_sent_saq_merchant_pci_email_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY saq_merchant_pci_email_sents
    ADD CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_pci_email_id_fkey FOREIGN KEY (saq_merchant_pci_email_id) REFERENCES saq_merchant_pci_emails(id);


--
-- Name: user_parent_user_secondary_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY "users"
    ADD CONSTRAINT user_parent_user_secondary_fkey FOREIGN KEY (parent_user_secondary) REFERENCES "users"(user_id);


--
-- Name: uw_approvalinfo_merchant_xref_approvalinfo_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_approvalinfo_merchant_xrefs
    ADD CONSTRAINT uw_approvalinfo_merchant_xref_approvalinfo_id_fkey FOREIGN KEY (approvalinfo_id) REFERENCES uw_approvalinfos(id);


--
-- Name: uw_approvalinfo_merchant_xref_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_approvalinfo_merchant_xrefs
    ADD CONSTRAINT uw_approvalinfo_merchant_xref_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: uw_approvalinfo_merchant_xref_verified_option_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_approvalinfo_merchant_xrefs
    ADD CONSTRAINT uw_approvalinfo_merchant_xref_verified_option_id_fkey FOREIGN KEY (verified_option_id) REFERENCES uw_verified_options(id);


--
-- Name: uw_infodoc_merchant_xref_infodoc_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_infodoc_merchant_xrefs
    ADD CONSTRAINT uw_infodoc_merchant_xref_infodoc_id_fkey FOREIGN KEY (infodoc_id) REFERENCES uw_infodocs(id);


--
-- Name: uw_infodoc_merchant_xref_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_infodoc_merchant_xrefs
    ADD CONSTRAINT uw_infodoc_merchant_xref_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: uw_infodoc_merchant_xref_received_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_infodoc_merchant_xrefs
    ADD CONSTRAINT uw_infodoc_merchant_xref_received_id_fkey FOREIGN KEY (received_id) REFERENCES uw_receiveds(id);


--
-- Name: uw_status_merchant_xref_merchant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_status_merchant_xrefs
    ADD CONSTRAINT uw_status_merchant_xref_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(merchant_id);


--
-- Name: uw_status_merchant_xref_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY uw_status_merchant_xrefs
    ADD CONSTRAINT uw_status_merchant_xref_status_id_fkey FOREIGN KEY (status_id) REFERENCES uw_statuses(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: achs; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE achs FROM PUBLIC;
REVOKE ALL ON TABLE achs FROM axia;
GRANT ALL ON TABLE achs TO axia;


--
-- Name: addresses; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE addresses FROM PUBLIC;
REVOKE ALL ON TABLE addresses FROM axia;
GRANT ALL ON TABLE addresses TO axia;


--
-- Name: address_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE address_types FROM PUBLIC;
REVOKE ALL ON TABLE address_types FROM axia;
GRANT ALL ON TABLE address_types TO axia;


--
-- Name: adjustments; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE adjustments FROM PUBLIC;
REVOKE ALL ON TABLE adjustments FROM axia;
GRANT ALL ON TABLE adjustments TO axia;


--
-- Name: adjustments_ts; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE adjustments_ts FROM PUBLIC;
REVOKE ALL ON TABLE adjustments_ts FROM axia;
GRANT ALL ON TABLE adjustments_ts TO axia;


--
-- Name: admin_entity_views; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE admin_entity_views FROM PUBLIC;
REVOKE ALL ON TABLE admin_entity_views FROM axia;
GRANT ALL ON TABLE admin_entity_views TO axia;


--
-- Name: bankcards; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE bankcards FROM PUBLIC;
REVOKE ALL ON TABLE bankcards FROM axia;
GRANT ALL ON TABLE bankcards TO axia;


--
-- Name: bets; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE bets FROM PUBLIC;
REVOKE ALL ON TABLE bets FROM axia;
GRANT ALL ON TABLE bets TO axia;


--
-- Name: card_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE card_types FROM PUBLIC;
REVOKE ALL ON TABLE card_types FROM axia;
GRANT ALL ON TABLE card_types TO axia;


--
-- Name: change_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE change_types FROM PUBLIC;
REVOKE ALL ON TABLE change_types FROM axia;
GRANT ALL ON TABLE change_types TO axia;


--
-- Name: check_guarantees; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE check_guarantees FROM PUBLIC;
REVOKE ALL ON TABLE check_guarantees FROM axia;
GRANT ALL ON TABLE check_guarantees TO axia;


--
-- Name: commission_report_olds; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE commission_report_olds FROM PUBLIC;
REVOKE ALL ON TABLE commission_report_olds FROM axia;
GRANT ALL ON TABLE commission_report_olds TO axia;


--
-- Name: merchants; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchants FROM PUBLIC;
REVOKE ALL ON TABLE merchants FROM axia;
GRANT ALL ON TABLE merchants TO axia;


--
-- Name: timeline_entries; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE timeline_entries FROM PUBLIC;
REVOKE ALL ON TABLE timeline_entries FROM axia;
GRANT ALL ON TABLE timeline_entries TO axia;


--
-- Name: debits; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE debits FROM PUBLIC;
REVOKE ALL ON TABLE debits FROM axia;
GRANT ALL ON TABLE debits TO axia;


--
-- Name: ebts; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE ebts FROM PUBLIC;
REVOKE ALL ON TABLE ebts FROM axia;
GRANT ALL ON TABLE ebts TO axia;


--
-- Name: entities; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE entities FROM PUBLIC;
REVOKE ALL ON TABLE entities FROM axia;
GRANT ALL ON TABLE entities TO axia;


--
-- Name: equipment_items; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_items FROM PUBLIC;
REVOKE ALL ON TABLE equipment_items FROM axia;
GRANT ALL ON TABLE equipment_items TO axia;


--
-- Name: equipment_programmings; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_programmings FROM PUBLIC;
REVOKE ALL ON TABLE equipment_programmings FROM axia;
GRANT ALL ON TABLE equipment_programmings TO axia;


--
-- Name: equipment_programming_type_xrefs; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_programming_type_xrefs FROM PUBLIC;
REVOKE ALL ON TABLE equipment_programming_type_xrefs FROM axia;
GRANT ALL ON TABLE equipment_programming_type_xrefs TO axia;


--
-- Name: equipment_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE equipment_types FROM PUBLIC;
REVOKE ALL ON TABLE equipment_types FROM axia;
GRANT ALL ON TABLE equipment_types TO axia;


--
-- Name: gift_cards; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE gift_cards FROM PUBLIC;
REVOKE ALL ON TABLE gift_cards FROM axia;
GRANT ALL ON TABLE gift_cards TO axia;


--
-- Name: groups; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE "groups" FROM PUBLIC;
REVOKE ALL ON TABLE "groups" FROM axia;
GRANT ALL ON TABLE "groups" TO axia;


--
-- Name: merchant_achs; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_achs FROM PUBLIC;
REVOKE ALL ON TABLE merchant_achs FROM axia;
GRANT ALL ON TABLE merchant_achs TO axia;


--
-- Name: merchant_ach_app_statuses; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_ach_app_statuses FROM PUBLIC;
REVOKE ALL ON TABLE merchant_ach_app_statuses FROM axia;
GRANT ALL ON TABLE merchant_ach_app_statuses TO axia;


--
-- Name: merchant_ach_billing_options; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_ach_billing_options FROM PUBLIC;
REVOKE ALL ON TABLE merchant_ach_billing_options FROM axia;
GRANT ALL ON TABLE merchant_ach_billing_options TO axia;


--
-- Name: merchant_banks; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_banks FROM PUBLIC;
REVOKE ALL ON TABLE merchant_banks FROM axia;
GRANT ALL ON TABLE merchant_banks TO axia;


--
-- Name: merchant_card_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_card_types FROM PUBLIC;
REVOKE ALL ON TABLE merchant_card_types FROM axia;
GRANT ALL ON TABLE merchant_card_types TO axia;


--
-- Name: merchant_changes; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_changes FROM PUBLIC;
REVOKE ALL ON TABLE merchant_changes FROM axia;
GRANT ALL ON TABLE merchant_changes TO axia;


--
-- Name: merchant_notes; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_notes FROM PUBLIC;
REVOKE ALL ON TABLE merchant_notes FROM axia;
GRANT ALL ON TABLE merchant_notes TO axia;


--
-- Name: merchant_owners; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_owners FROM PUBLIC;
REVOKE ALL ON TABLE merchant_owners FROM axia;
GRANT ALL ON TABLE merchant_owners TO axia;


--
-- Name: merchant_references; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_references FROM PUBLIC;
REVOKE ALL ON TABLE merchant_references FROM axia;
GRANT ALL ON TABLE merchant_references TO axia;


--
-- Name: merchant_reference_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE merchant_reference_types FROM PUBLIC;
REVOKE ALL ON TABLE merchant_reference_types FROM axia;
GRANT ALL ON TABLE merchant_reference_types TO axia;


--
-- Name: networks; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE networks FROM PUBLIC;
REVOKE ALL ON TABLE networks FROM axia;
GRANT ALL ON TABLE networks TO axia;


--
-- Name: note_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE note_types FROM PUBLIC;
REVOKE ALL ON TABLE note_types FROM axia;
GRANT ALL ON TABLE note_types TO axia;


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
-- Name: permissions; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE permissions FROM PUBLIC;
REVOKE ALL ON TABLE permissions FROM axia;
GRANT ALL ON TABLE permissions TO axia;


--
-- Name: permission_groups; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE permission_groups FROM PUBLIC;
REVOKE ALL ON TABLE permission_groups FROM axia;
GRANT ALL ON TABLE permission_groups TO axia;


--
-- Name: pricing_matrixes; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE pricing_matrixes FROM PUBLIC;
REVOKE ALL ON TABLE pricing_matrixes FROM axia;
GRANT ALL ON TABLE pricing_matrixes TO axia;


--
-- Name: products_and_services; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE products_and_services FROM PUBLIC;
REVOKE ALL ON TABLE products_and_services FROM axia;
GRANT ALL ON TABLE products_and_services TO axia;


--
-- Name: products_services_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE products_services_types FROM PUBLIC;
REVOKE ALL ON TABLE products_services_types FROM axia;
GRANT ALL ON TABLE products_services_types TO axia;


--
-- Name: referers; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE referers FROM PUBLIC;
REVOKE ALL ON TABLE referers FROM axia;
GRANT ALL ON TABLE referers TO axia;


--
-- Name: rep_cost_structures; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE rep_cost_structures FROM PUBLIC;
REVOKE ALL ON TABLE rep_cost_structures FROM axia;
GRANT ALL ON TABLE rep_cost_structures TO axia;


--
-- Name: residual_pricings; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE residual_pricings FROM PUBLIC;
REVOKE ALL ON TABLE residual_pricings FROM axia;
GRANT ALL ON TABLE residual_pricings TO axia;


--
-- Name: residual_reports; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE residual_reports FROM PUBLIC;
REVOKE ALL ON TABLE residual_reports FROM axia;
GRANT ALL ON TABLE residual_reports TO axia;


--
-- Name: sales_goals; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE sales_goals FROM PUBLIC;
REVOKE ALL ON TABLE sales_goals FROM axia;
GRANT ALL ON TABLE sales_goals TO axia;


--
-- Name: sales_goal_archives; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE sales_goal_archives FROM PUBLIC;
REVOKE ALL ON TABLE sales_goal_archives FROM axia;
GRANT ALL ON TABLE sales_goal_archives TO axia;


--
-- Name: shipping_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE shipping_types FROM PUBLIC;
REVOKE ALL ON TABLE shipping_types FROM axia;
GRANT ALL ON TABLE shipping_types TO axia;


--
-- Name: system_transactions; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE system_transactions FROM PUBLIC;
REVOKE ALL ON TABLE system_transactions FROM axia;
GRANT ALL ON TABLE system_transactions TO axia;


--
-- Name: timeline_items; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE timeline_items FROM PUBLIC;
REVOKE ALL ON TABLE timeline_items FROM axia;
GRANT ALL ON TABLE timeline_items TO axia;


--
-- Name: transaction_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE transaction_types FROM PUBLIC;
REVOKE ALL ON TABLE transaction_types FROM axia;
GRANT ALL ON TABLE transaction_types TO axia;


--
-- Name: users; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE "users" FROM PUBLIC;
REVOKE ALL ON TABLE "users" FROM axia;
GRANT ALL ON TABLE "users" TO axia;


--
-- Name: user_bet_tables; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE user_bet_tables FROM PUBLIC;
REVOKE ALL ON TABLE user_bet_tables FROM axia;
GRANT ALL ON TABLE user_bet_tables TO axia;


--
-- Name: user_permissions; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE user_permissions FROM PUBLIC;
REVOKE ALL ON TABLE user_permissions FROM axia;
GRANT ALL ON TABLE user_permissions TO axia;


--
-- Name: user_types; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE user_types FROM PUBLIC;
REVOKE ALL ON TABLE user_types FROM axia;
GRANT ALL ON TABLE user_types TO axia;


--
-- Name: vendors; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE vendors FROM PUBLIC;
REVOKE ALL ON TABLE vendors FROM axia;
GRANT ALL ON TABLE vendors TO axia;


--
-- Name: virtual_checks; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE virtual_checks FROM PUBLIC;
REVOKE ALL ON TABLE virtual_checks FROM axia;
GRANT ALL ON TABLE virtual_checks TO axia;


--
-- Name: virtual_check_webs; Type: ACL; Schema: public; Owner: axia
--

REVOKE ALL ON TABLE virtual_check_webs FROM PUBLIC;
REVOKE ALL ON TABLE virtual_check_webs FROM axia;
GRANT ALL ON TABLE virtual_check_webs TO axia;


--
-- PostgreSQL database dump complete
--

