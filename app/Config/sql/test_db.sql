--
-- PostgreSQL database dump
--

-- Dumped from database version 11.11 (Ubuntu 11.11-1.pgdg20.04+1)
-- Dumped by pg_dump version 11.11 (Ubuntu 11.11-1.pgdg20.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- Name: pp_types; Type: TYPE; Schema: public; Owner: axia
--

CREATE TYPE public.pp_types AS ENUM (
    'percentage',
    'points',
    'percentage-grossprofit',
    'points-calculateonly'
);


ALTER TYPE public.pp_types OWNER TO axia;

--
-- Name: uw_verified_type; Type: TYPE; Schema: public; Owner: axia
--

CREATE TYPE public.uw_verified_type AS ENUM (
    'nrp',
    'yn',
    'match'
);


ALTER TYPE public.uw_verified_type OWNER TO axia;

--
-- Name: drop_not_null_on_old(); Type: FUNCTION; Schema: public; Owner: axia
--

CREATE FUNCTION public.drop_not_null_on_old() RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
	rec RECORD;
	cmd text;
BEGIN
	cmd := '';

	FOR rec IN SELECT
			'ALTER TABLE ' || table_name || ' ALTER COLUMN "'
				|| column_name || '" DROP NOT NULL;' AS name
		FROM
			information_schema.columns
		WHERE
			column_name ~ '_old' AND table_schema = 'public'
	LOOP
		cmd := cmd || rec.name;
	END LOOP;

	EXECUTE cmd;
	RETURN;
END;
$$;


ALTER FUNCTION public.drop_not_null_on_old() OWNER TO axia;

--
-- Name: make_date(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.make_date(year integer, month integer, day integer) RETURNS date
    LANGUAGE sql IMMUTABLE STRICT
    AS $$
SELECT format('%s-%s-%s', year, month, day)::date;
$$;


ALTER FUNCTION public.make_date(year integer, month integer, day integer) OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: ach_providers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.ach_providers (
    id uuid NOT NULL,
    provider_name character varying(255) NOT NULL
);


ALTER TABLE public.ach_providers OWNER TO axia;

--
-- Name: ach_rep_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.ach_rep_costs (
    id uuid NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    ach_provider_id uuid NOT NULL,
    rep_rate_pct numeric,
    rep_per_item numeric,
    rep_monthly_cost numeric
);


ALTER TABLE public.ach_rep_costs OWNER TO axia;

--
-- Name: aches; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.aches (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    ach_mid character varying(36) DEFAULT NULL::character varying,
    ach_expected_annual_sales numeric(15,4) DEFAULT NULL::numeric,
    ach_average_transaction numeric(15,4) DEFAULT NULL::numeric,
    ach_estimated_max_transaction numeric(15,4) DEFAULT NULL::numeric,
    ach_written_pre_auth numeric(15,4) DEFAULT NULL::numeric,
    ach_nonwritten_pre_auth numeric(15,4) DEFAULT NULL::numeric,
    ach_merchant_initiated_perc numeric(15,4) DEFAULT NULL::numeric,
    ach_consumer_initiated_perc numeric(15,4) DEFAULT NULL::numeric,
    ach_monthly_gateway_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_monthly_minimum_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_batch_upload_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_reject_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_add_bank_orig_ident_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_file_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_eft_ccd_nw numeric(15,4) DEFAULT NULL::numeric,
    ach_eft_ccd_w numeric(15,4) DEFAULT NULL::numeric,
    ach_eft_ppd_nw numeric(15,4) DEFAULT NULL::numeric,
    ach_eft_ppd_w numeric(15,4) DEFAULT NULL::numeric,
    ach_application_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_expedite_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_tele_training_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_mi_w_dsb_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_mi_w_dsb_routing_number character varying(255),
    ach_mi_w_dsb_account_number character varying(255),
    ach_mi_w_fee_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_mi_w_fee_routing_number character varying(255),
    ach_mi_w_fee_account_number character varying(255),
    ach_mi_w_rej_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_mi_w_rej_routing_number character varying(255),
    ach_mi_w_rej_account_number character varying(255),
    ach_mi_nw_dsb_account_number character varying(255),
    ach_mi_nw_dsb_routing_number character varying(255),
    ach_mi_nw_dsb_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_mi_nw_fee_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_mi_nw_fee_routing_number character varying(255),
    ach_mi_nw_fee_account_number character varying(255),
    ach_mi_nw_rej_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_mi_nw_rej_routing_number character varying(255),
    ach_mi_nw_rej_account_number character varying(255),
    ach_ci_w_dsb_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_ci_w_dsb_routing_number character varying(255),
    ach_ci_w_dsb_account_number character varying(255),
    ach_ci_w_fee_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_ci_w_fee_routing_number character varying(255),
    ach_ci_w_fee_account_number character varying(255),
    ach_ci_w_rej_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_ci_w_rej_routing_number character varying(255),
    ach_ci_w_rej_account_number character varying(255),
    ach_ci_nw_dsb_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_ci_nw_dsb_routing_number character varying(255),
    ach_ci_nw_dsb_account_number character varying(255),
    ach_ci_nw_fee_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_ci_nw_fee_routing_number character varying(255),
    ach_ci_nw_fee_account_number character varying(255),
    ach_ci_nw_rej_bank_name character varying(50) DEFAULT NULL::character varying,
    ach_ci_nw_rej_routing_number character varying(255),
    ach_ci_nw_rej_account_number character varying(255),
    ach_rate numeric(15,4) DEFAULT NULL::numeric,
    ach_per_item_fee numeric,
    ach_provider_id uuid,
    ach_risk_assessment numeric
);


ALTER TABLE public.aches OWNER TO axia;

--
-- Name: addl_amex_rep_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.addl_amex_rep_costs (
    id uuid NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    conversion_fee numeric,
    sys_processing_fee numeric
);


ALTER TABLE public.addl_amex_rep_costs OWNER TO axia;

--
-- Name: address_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.address_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    address_type_old character varying(36),
    address_type_description character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.address_types OWNER TO axia;

--
-- Name: addresses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.addresses (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    address_id_old character varying(36),
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    address_type_id uuid NOT NULL,
    address_type_old character varying(36),
    merchant_owner_id uuid,
    merchant_owner_id_old character varying(36) DEFAULT NULL::character varying,
    address_title character varying(50) DEFAULT NULL::character varying,
    address_street character varying(100) DEFAULT NULL::character varying,
    address_city character varying(50) DEFAULT NULL::character varying,
    address_state character varying(2) DEFAULT NULL::character varying,
    address_zip character varying(20) DEFAULT NULL::character varying,
    address_phone character varying(20) DEFAULT NULL::character varying,
    address_fax character varying(20) DEFAULT NULL::character varying,
    address_phone2 character varying(20) DEFAULT NULL::character varying,
    address_phone_ext character varying(5) DEFAULT NULL::character varying,
    address_phone2_ext character varying(5) DEFAULT NULL::character varying
);


ALTER TABLE public.addresses OWNER TO axia;

--
-- Name: adjustments; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.adjustments (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    adj_seq_number_old integer,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    adj_date date,
    adj_description character varying(100) DEFAULT NULL::character varying,
    adj_amount numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.adjustments OWNER TO axia;

--
-- Name: admin_entity_views; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.admin_entity_views (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    entity_id uuid NOT NULL,
    entity_old character varying(36)
);


ALTER TABLE public.admin_entity_views OWNER TO axia;

--
-- Name: amexes; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.amexes (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    amex_processing_rate numeric(15,4) DEFAULT NULL::numeric,
    amex_per_item_fee numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.amexes OWNER TO axia;

--
-- Name: api_configurations; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.api_configurations (
    id uuid NOT NULL,
    configuration_name character varying(150) NOT NULL,
    auth_type character varying(150),
    instance_url character varying(250),
    authorization_url character varying(250),
    access_token_url character varying(250),
    redirect_url character varying(250),
    client_id character varying(250),
    client_secret character varying(250),
    access_token character varying(250),
    access_token_lifetime_hours integer,
    refresh_token character varying(250),
    issued_at character varying(100)
);


ALTER TABLE public.api_configurations OWNER TO axia;

--
-- Name: app_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.app_statuses (
    id uuid NOT NULL,
    merchant_ach_app_status_id uuid,
    rep_cost numeric,
    axia_cost numeric,
    rep_expedite_cost numeric,
    axia_expedite_cost_tsys numeric,
    axia_expedite_cost_sage numeric,
    user_compensation_profile_id uuid
);


ALTER TABLE public.app_statuses OWNER TO axia;

--
-- Name: articles; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.articles (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    title character varying(100) NOT NULL,
    body character varying(500) NOT NULL
);


ALTER TABLE public.articles OWNER TO axia;

--
-- Name: associated_external_records; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.associated_external_records (
    id uuid NOT NULL,
    external_system_name character varying(200) NOT NULL,
    merchant_id uuid NOT NULL
);


ALTER TABLE public.associated_external_records OWNER TO axia;

--
-- Name: associated_users; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.associated_users (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    associated_user_id uuid NOT NULL,
    role character varying(36) NOT NULL,
    permission_level character varying(36) NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    main_association boolean DEFAULT false
);


ALTER TABLE public.associated_users OWNER TO axia;

--
-- Name: attrition_ratios; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.attrition_ratios (
    id uuid NOT NULL,
    associated_user_id uuid,
    percentage numeric,
    user_compensation_profile_id uuid
);


ALTER TABLE public.attrition_ratios OWNER TO axia;

--
-- Name: authorizes; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.authorizes (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid,
    merchant_id_old character varying(36),
    mid character varying(20) DEFAULT NULL::character varying,
    transaction_fee numeric(15,4) DEFAULT NULL::numeric,
    monthly_fee numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.authorizes OWNER TO axia;

--
-- Name: back_end_networks; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.back_end_networks (
    id uuid NOT NULL,
    network_description character varying(50)
);


ALTER TABLE public.back_end_networks OWNER TO axia;

--
-- Name: background_jobs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.background_jobs (
    id uuid NOT NULL,
    job_status_id uuid NOT NULL,
    description character varying(200),
    modified timestamp without time zone NOT NULL
);


ALTER TABLE public.background_jobs OWNER TO axia;

--
-- Name: bankcards; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.bankcards (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    bc_mid character varying(20) DEFAULT NULL::character varying,
    bet_id uuid,
    bet_code_old character varying(36) DEFAULT NULL::character varying,
    bc_processing_rate numeric(15,4) DEFAULT NULL::numeric,
    bc_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    bc_average_ticket numeric(15,4) DEFAULT NULL::numeric,
    bc_max_transaction_amount numeric(15,4) DEFAULT NULL::numeric,
    bc_card_present_swipe numeric(15,4) DEFAULT NULL::numeric,
    bc_card_not_present numeric(15,4) DEFAULT NULL::numeric,
    bc_card_present_imprint numeric(15,4) DEFAULT NULL::numeric,
    bc_direct_to_consumer numeric(15,4) DEFAULT NULL::numeric,
    bc_business_to_business numeric(15,4) DEFAULT NULL::numeric,
    bc_government numeric(15,4) DEFAULT NULL::numeric,
    bc_annual_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_min_month_process_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_chargeback_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_aru_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_voice_auth_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_te_amex_number character varying(255) DEFAULT NULL::character varying,
    bc_te_amex_auth_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_te_diners_club_number character varying(255) DEFAULT NULL::character varying,
    bc_te_diners_club_auth_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_te_discover_number character varying(255) DEFAULT NULL::character varying,
    bc_te_discover_auth_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_te_jcb_number character varying(255) DEFAULT NULL::character varying,
    bc_te_jcb_auth_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_monthly_support_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_online_mer_report_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_mobile_access_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_mobile_transaction_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_application_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_expedite_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_mobile_setup_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_equip_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_phys_prod_tele_train_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_pt_equip_reprog_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_vt_monthly_support_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_vt_gateway_access_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_vt_application_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_vt_expedite_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_vt_prod_tele_train_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_vt_lease_rental_deposit numeric(15,4) DEFAULT NULL::numeric,
    bc_hidden_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_te_amex_number_disp character varying(4) DEFAULT NULL::character varying,
    bc_te_diners_club_number_disp character varying(4) DEFAULT NULL::character varying,
    bc_te_discover_number_disp character varying(4) DEFAULT NULL::character varying,
    bc_te_jcb_number_disp character varying(4) DEFAULT NULL::character varying,
    bc_hidden_per_item_fee_cross numeric(15,4) DEFAULT NULL::numeric,
    bc_eft_secure_flag integer,
    bc_pos_partner_flag integer,
    bc_pt_online_rep_report_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_micros_ip_flag integer,
    bc_micros_dialup_flag integer,
    bc_micros_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    bc_te_num_items numeric(15,4) DEFAULT NULL::numeric,
    bc_wireless_flag integer,
    bc_wireless_terminals integer,
    bc_usaepay_flag integer,
    bc_epay_retail_flag integer,
    usaepay_rep_gtwy_cost_id uuid,
    bc_usaepay_rep_gtwy_cost_id_old character varying(36) DEFAULT NULL::character varying,
    usaepay_rep_gtwy_add_cost_id uuid,
    bc_usaepay_rep_gtwy_add_cost_id_old character varying(36) DEFAULT NULL::character varying,
    bc_card_not_present_internet numeric(15,4) DEFAULT NULL::numeric,
    bc_tgate_flag integer,
    bc_petro_flag integer,
    bc_risk_assessment numeric(15,4) DEFAULT NULL::numeric,
    bc_gw_gateway_id_old character varying(36) DEFAULT NULL::character varying,
    gateway_id uuid,
    bc_gw_rep_rate numeric(15,4) DEFAULT NULL::numeric,
    bc_gw_rep_per_item numeric(15,4) DEFAULT NULL::numeric,
    bc_gw_rep_statement numeric(15,4) DEFAULT NULL::numeric,
    bc_gw_rep_features text
);


ALTER TABLE public.bankcards OWNER TO axia;

--
-- Name: bet_networks; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.bet_networks (
    id uuid NOT NULL,
    name character varying(36) NOT NULL,
    is_active integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.bet_networks OWNER TO axia;

--
-- Name: bet_tables; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.bet_tables (
    id uuid NOT NULL,
    name character varying(64) NOT NULL,
    bet_extra_pct numeric DEFAULT 0,
    card_type_id uuid,
    is_enabled boolean DEFAULT true NOT NULL
);


ALTER TABLE public.bet_tables OWNER TO axia;

--
-- Name: bets; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.bets (
    id uuid NOT NULL,
    bet_network_id uuid NOT NULL,
    bet_table_id uuid NOT NULL,
    card_type_id uuid NOT NULL,
    pct_cost numeric,
    pi_cost numeric,
    additional_pct numeric,
    sales_cost numeric,
    auth_cost numeric,
    dial_auth_cost numeric,
    non_dial_auth_cost numeric,
    non_dial_auto_cost numeric,
    settlement_cost numeric,
    user_compensation_profile_id uuid,
    non_dial_sales_cost numeric,
    dial_sales_cost numeric
);


ALTER TABLE public.bets OWNER TO axia;

--
-- Name: brands; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.brands (
    id uuid NOT NULL,
    name text
);


ALTER TABLE public.brands OWNER TO axia;

--
-- Name: cancellation_fees; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.cancellation_fees (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    cancellation_fee_id_old character varying(36),
    cancellation_fee_description character varying(50) NOT NULL
);


ALTER TABLE public.cancellation_fees OWNER TO axia;

--
-- Name: card_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.card_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    card_type_old character varying(36),
    card_type_description character varying(20) DEFAULT NULL::character varying
);


ALTER TABLE public.card_types OWNER TO axia;

--
-- Name: change_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.change_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    change_type_old integer,
    change_type_description character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.change_types OWNER TO axia;

--
-- Name: check_guarantee_providers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.check_guarantee_providers (
    id uuid NOT NULL,
    provider_name character varying(255) NOT NULL
);


ALTER TABLE public.check_guarantee_providers OWNER TO axia;

--
-- Name: check_guarantee_service_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.check_guarantee_service_types (
    id uuid NOT NULL,
    service_type character varying(255) NOT NULL
);


ALTER TABLE public.check_guarantee_service_types OWNER TO axia;

--
-- Name: check_guarantees; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.check_guarantees (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    cg_mid character varying(20) DEFAULT NULL::character varying,
    cg_station_number character varying(20) DEFAULT NULL::character varying,
    cg_account_number character varying(20) DEFAULT NULL::character varying,
    cg_transaction_rate numeric(15,4) DEFAULT NULL::numeric,
    cg_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    cg_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    cg_monthly_minimum_fee numeric(15,4) DEFAULT NULL::numeric,
    cg_application_fee numeric(15,4) DEFAULT NULL::numeric,
    check_guarantee_provider_id uuid,
    check_guarantee_service_type_id uuid,
    rep_processing_rate_pct numeric,
    rep_per_item_cost numeric,
    rep_monthly_cost numeric,
    cg_risk_assessment numeric
);


ALTER TABLE public.check_guarantees OWNER TO axia;

--
-- Name: clients; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.clients (
    id uuid NOT NULL,
    client_id_global integer NOT NULL,
    client_name_global character varying(100) NOT NULL
);


ALTER TABLE public.clients OWNER TO axia;

--
-- Name: commission_fees; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.commission_fees (
    id uuid NOT NULL,
    associated_user_id uuid,
    is_do_not_display integer DEFAULT 0 NOT NULL,
    app_fee_profit numeric,
    app_fee_loss numeric,
    non_app_fee_profit numeric,
    non_app_fee_loss numeric,
    created timestamp without time zone,
    modified timestamp without time zone,
    user_compensation_profile_id uuid
);


ALTER TABLE public.commission_fees OWNER TO axia;

--
-- Name: commission_pricings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.commission_pricings (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    c_month integer DEFAULT 0 NOT NULL,
    c_year integer DEFAULT 0 NOT NULL,
    multiple numeric(15,4) DEFAULT NULL::numeric,
    r_rate_pct numeric(15,4) DEFAULT NULL::numeric,
    r_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    r_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    m_rate_pct numeric(15,4) DEFAULT NULL::numeric,
    m_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    m_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    m_avg_ticket numeric(15,4) DEFAULT NULL::numeric,
    m_monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    bet_table_id uuid,
    bet_code_old character varying(36) DEFAULT NULL::character varying,
    referer_id uuid,
    ref_seq_number_old integer,
    bet_extra_pct numeric(15,4) DEFAULT NULL::numeric,
    reseller_id uuid,
    res_seq_number_old integer,
    products_services_type_id uuid,
    products_services_type_id_old character varying(36) DEFAULT 'BC'::character varying,
    num_items numeric(15,4) DEFAULT NULL::numeric,
    ref_p_value numeric(15,4) DEFAULT NULL::numeric,
    res_p_value numeric(15,4) DEFAULT NULL::numeric,
    r_risk_assessment numeric(15,4) DEFAULT NULL::numeric,
    ref_p_type text,
    res_p_type text,
    ref_p_pct integer,
    res_p_pct integer,
    partner_rate_pct numeric,
    partner_per_item numeric,
    partner_statement_fee numeric,
    rep_product_profit_pct numeric,
    manager_id uuid,
    secondary_manager_id uuid,
    partner_id uuid,
    referrer_id uuid,
    product_group_id uuid,
    rep_gross_profit numeric DEFAULT 0,
    rep_pct_of_gross numeric DEFAULT 0,
    partner_rep_rate numeric DEFAULT 0,
    partner_rep_per_item_fee numeric DEFAULT 0,
    partner_rep_statement_fee numeric DEFAULT 0,
    partner_rep_gross_profit numeric DEFAULT 0,
    partner_rep_pct_of_gross numeric DEFAULT 0,
    manager_rate numeric DEFAULT 0,
    manager_per_item_fee numeric DEFAULT 0,
    manager_statement_fee numeric DEFAULT 0,
    manager_gross_profit numeric DEFAULT 0,
    manager_pct_of_gross numeric DEFAULT 0,
    manager2_rate numeric DEFAULT 0,
    manager2_per_item_fee numeric DEFAULT 0,
    manager2_statement_fee numeric DEFAULT 0,
    manager2_gross_profit numeric DEFAULT 0,
    manager2_pct_of_gross numeric DEFAULT 0,
    partner_rate numeric DEFAULT 0,
    partner_per_item_fee numeric DEFAULT 0,
    partner_gross_profit numeric DEFAULT 0,
    partner_pct_of_gross numeric DEFAULT 0,
    referrer_rate numeric DEFAULT 0,
    referrer_per_item_fee numeric DEFAULT 0,
    referrer_statement_fee numeric DEFAULT 0,
    referrer_gross_profit numeric DEFAULT 0,
    referrer_pct_of_gross numeric DEFAULT 0,
    reseller_rate numeric DEFAULT 0,
    reseller_per_item_fee numeric DEFAULT 0,
    reseller_statement_fee numeric DEFAULT 0,
    reseller_gross_profit numeric DEFAULT 0,
    reseller_pct_of_gross numeric DEFAULT 0,
    merchant_state character varying(255) DEFAULT NULL::character varying,
    multiple_amount numeric DEFAULT 0,
    r_profit_pct numeric,
    r_profit_amount numeric,
    partner_rep_profit_pct numeric,
    partner_rep_profit_amount numeric,
    manager_profit_pct numeric,
    manager_profit_amount numeric,
    manager2_profit_pct numeric,
    manager2_profit_amount numeric,
    partner_profit_pct numeric,
    partner_profit_amount numeric,
    referrer_profit_pct numeric,
    referrer_profit_amount numeric,
    reseller_profit_pct numeric,
    reseller_profit_amount numeric,
    gross_profit numeric(15,4) DEFAULT NULL::numeric,
    original_m_rate_pct numeric,
    manager_multiple numeric,
    manager_multiple_amount numeric,
    manager2_multiple numeric,
    manager2_multiple_amount numeric
);


ALTER TABLE public.commission_pricings OWNER TO axia;

--
-- Name: commission_reports; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.commission_reports (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    c_month integer,
    c_year integer,
    c_retail numeric(15,4) DEFAULT NULL::numeric,
    c_rep_cost numeric(15,4) DEFAULT NULL::numeric,
    c_shipping numeric(15,4) DEFAULT NULL::numeric,
    c_app_rtl numeric(15,4) DEFAULT NULL::numeric,
    c_app_cost numeric(15,4) DEFAULT NULL::numeric,
    c_install numeric(15,4) DEFAULT NULL::numeric,
    c_expecting numeric(15,4) DEFAULT NULL::numeric,
    c_from_nw1 numeric(15,4) DEFAULT NULL::numeric,
    c_other_cost numeric(15,4) DEFAULT NULL::numeric,
    c_from_other numeric(15,4) DEFAULT NULL::numeric,
    c_business numeric(15,4) DEFAULT NULL::numeric,
    user_id uuid NOT NULL,
    user_id_old character varying(36) DEFAULT NULL::character varying,
    status character varying(5) DEFAULT NULL::character varying,
    order_id uuid,
    order_id_old character varying(36) DEFAULT NULL::character varying,
    axia_invoice_number bigint,
    merchant_ach_id uuid,
    ach_seq_number_old integer,
    description character varying(200) DEFAULT NULL::character varying,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36) DEFAULT NULL::character varying,
    split_commissions boolean NOT NULL,
    referer_id uuid,
    ref_seq_number_old integer,
    reseller_id uuid,
    res_seq_number_old integer,
    partner_id uuid,
    partner_id_old character varying(36) DEFAULT NULL::character varying,
    partner_exclude_volume boolean,
    partner_cost numeric,
    shipping_type_id uuid,
    partner_rep_profit numeric,
    partner_profit numeric,
    tax_amount numeric
);


ALTER TABLE public.commission_reports OWNER TO axia;

--
-- Name: control_scan_merchant_sync_results; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.control_scan_merchant_sync_results (
    id uuid NOT NULL,
    new_merchant_count integer DEFAULT 0 NOT NULL,
    updated_merchant_count integer DEFAULT 0 NOT NULL,
    unboarded_merchant_count integer DEFAULT 0 NOT NULL,
    created timestamp without time zone
);


ALTER TABLE public.control_scan_merchant_sync_results OWNER TO axia;

--
-- Name: debit_acquirers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.debit_acquirers (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    debit_acquirer_id_old character varying(36),
    debit_acquirers character varying(137) NOT NULL
);


ALTER TABLE public.debit_acquirers OWNER TO axia;

--
-- Name: debits; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.debits (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    mid character varying(20) DEFAULT NULL::character varying,
    transaction_fee numeric(15,4) DEFAULT NULL::numeric,
    monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    monthly_num_items numeric(15,4) DEFAULT NULL::numeric,
    rate_pct numeric(15,4) DEFAULT NULL::numeric,
    debit_acquirer_id uuid,
    debit_acquirer_id_old character varying(36) DEFAULT NULL::character varying
);


ALTER TABLE public.debits OWNER TO axia;

--
-- Name: discover_bets; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.discover_bets (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    bet_id uuid,
    bet_code_old character varying(36),
    bet_extra_pct numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.discover_bets OWNER TO axia;

--
-- Name: discover_user_bet_tables; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.discover_user_bet_tables (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    bet_id uuid,
    bet_code_old character varying(50),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    network character varying(20) NOT NULL,
    rate numeric(15,4) DEFAULT NULL::numeric,
    pi numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.discover_user_bet_tables OWNER TO axia;

--
-- Name: discovers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.discovers (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    bet_id uuid,
    bet_code_old character varying(36) DEFAULT NULL::character varying,
    disc_processing_rate numeric(15,4) DEFAULT NULL::numeric,
    disc_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    disc_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    disc_monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    disc_average_ticket numeric(15,4) DEFAULT NULL::numeric,
    disc_risk_assessment numeric(15,4) DEFAULT NULL::numeric,
    gateway_id uuid,
    gateway_id_old character varying(36) DEFAULT NULL::character varying,
    disc_gw_rep_rate numeric(15,4) DEFAULT NULL::numeric,
    disc_gw_rep_per_item numeric(15,4) DEFAULT NULL::numeric,
    disc_gw_rep_features text
);


ALTER TABLE public.discovers OWNER TO axia;

--
-- Name: ebts; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.ebts (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    mid character varying(20) DEFAULT NULL::character varying,
    transaction_fee numeric(15,4) DEFAULT NULL::numeric,
    monthly_fee numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.ebts OWNER TO axia;

--
-- Name: entities; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.entities (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    entity_old character varying(36),
    entity_name character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.entities OWNER TO axia;

--
-- Name: equipment_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.equipment_costs (
    id uuid NOT NULL,
    equipment_item_id uuid,
    rep_cost numeric,
    user_compensation_profile_id uuid
);


ALTER TABLE public.equipment_costs OWNER TO axia;

--
-- Name: equipment_items; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.equipment_items (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    equipment_item_old character varying(36),
    equipment_type_id uuid NOT NULL,
    equipment_type_old character varying(36),
    equipment_item_description character varying(100) DEFAULT NULL::character varying,
    equipment_item_true_price numeric(15,4) DEFAULT NULL::numeric,
    equipment_item_rep_price numeric(15,4) DEFAULT NULL::numeric,
    active integer DEFAULT 1,
    warranty integer
);


ALTER TABLE public.equipment_items OWNER TO axia;

--
-- Name: equipment_programming_type_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.equipment_programming_type_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    programming_id_old character varying(36),
    programming_type character varying(36) NOT NULL,
    equipment_programming_id uuid
);


ALTER TABLE public.equipment_programming_type_xrefs OWNER TO axia;

--
-- Name: equipment_programmings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.equipment_programmings (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    equipment_programming_id uuid,
    programming_id_old character varying(36),
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    terminal_number character varying(20) DEFAULT NULL::character varying,
    hardware_serial character varying(20) DEFAULT NULL::character varying,
    terminal_type character varying(20) DEFAULT NULL::character varying,
    network character varying(20) DEFAULT NULL::character varying,
    provider character varying(20) DEFAULT NULL::character varying,
    app_type character varying(20) DEFAULT NULL::character varying,
    status character varying(5) DEFAULT NULL::character varying,
    date_entered date,
    date_changed date,
    user_id uuid,
    user_id_old character varying(36) DEFAULT NULL::character varying,
    serial_number character varying(20) DEFAULT NULL::character varying,
    pin_pad character varying(20) DEFAULT NULL::character varying,
    printer character varying(20) DEFAULT NULL::character varying,
    auto_close character varying(20) DEFAULT NULL::character varying,
    chain character varying(6) DEFAULT NULL::character varying,
    agent character varying(6) DEFAULT NULL::character varying,
    gateway_id uuid,
    gateway_id_old character varying(36) DEFAULT NULL::character varying,
    version character varying(20) DEFAULT NULL::character varying
);


ALTER TABLE public.equipment_programmings OWNER TO axia;

--
-- Name: equipment_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.equipment_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    equipment_type_old character varying(36),
    equipment_type_description character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.equipment_types OWNER TO axia;

--
-- Name: external_record_fields; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.external_record_fields (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    associated_external_record_id uuid,
    field_name character varying(255) NOT NULL,
    api_field_name character varying(255) NOT NULL,
    value character varying(255)
);


ALTER TABLE public.external_record_fields OWNER TO axia;

--
-- Name: gateway0s; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.gateway0s (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    gw_rate numeric(15,4) DEFAULT NULL::numeric,
    gw_per_item numeric(15,4) DEFAULT NULL::numeric,
    gw_statement numeric(15,4) DEFAULT NULL::numeric,
    gw_epay_retail_num integer,
    gw_usaepay_rep_gtwy_cost_id uuid,
    gw_usaepay_rep_gtwy_cost_id_old character varying(36) DEFAULT NULL::character varying,
    gw_usaepay_rep_gtwy_add_cost_id uuid,
    gw_usaepay_rep_gtwy_add_cost_id_old character varying(36) DEFAULT NULL::character varying
);


ALTER TABLE public.gateway0s OWNER TO axia;

--
-- Name: gateway1s; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.gateway1s (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    gw1_rate numeric(15,4) DEFAULT NULL::numeric,
    gw1_per_item numeric(15,4) DEFAULT NULL::numeric,
    gw1_statement numeric(15,4) DEFAULT NULL::numeric,
    gw1_rep_rate numeric(15,4) DEFAULT NULL::numeric,
    gw1_rep_per_item numeric(15,4) DEFAULT NULL::numeric,
    gw1_rep_statement numeric(15,4) DEFAULT NULL::numeric,
    gw1_rep_features text,
    gateway_id uuid NOT NULL,
    gateway_id_old character varying(36),
    gw1_monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    gw1_monthly_num_items numeric(15,4) DEFAULT NULL::numeric,
    gw1_mid character varying(20),
    has_m_link_pi boolean DEFAULT false,
    addl_rep_statement_cost numeric
);


ALTER TABLE public.gateway1s OWNER TO axia;

--
-- Name: gateway2s; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.gateway2s (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    gw2_rate numeric(15,4) DEFAULT NULL::numeric,
    gw2_per_item numeric(15,4) DEFAULT NULL::numeric,
    gw2_statement numeric(15,4) DEFAULT NULL::numeric,
    gw2_rep_rate numeric(15,4) DEFAULT NULL::numeric,
    gw2_rep_per_item numeric(15,4) DEFAULT NULL::numeric,
    gw2_rep_statement numeric(15,4) DEFAULT NULL::numeric,
    gw2_rep_features text,
    gateway_id uuid NOT NULL,
    gateway_id_old character varying(36),
    gw2_monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    gw2_monthly_num_items numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.gateway2s OWNER TO axia;

--
-- Name: gateway_cost_structures; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.gateway_cost_structures (
    id uuid NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    gateway_id uuid NOT NULL,
    rep_monthly_cost numeric,
    rep_rate_pct numeric,
    rep_per_item numeric
);


ALTER TABLE public.gateway_cost_structures OWNER TO axia;

--
-- Name: gateways; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.gateways (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(57) NOT NULL,
    enabled boolean DEFAULT true NOT NULL,
    "position" integer
);


ALTER TABLE public.gateways OWNER TO axia;

--
-- Name: gift_card_providers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.gift_card_providers (
    id uuid NOT NULL,
    provider_name character varying(255) NOT NULL
);


ALTER TABLE public.gift_card_providers OWNER TO axia;

--
-- Name: gift_cards; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.gift_cards (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    gc_mid character varying(20) DEFAULT NULL::character varying,
    gc_loyalty_item_fee numeric,
    gc_gift_item_fee numeric,
    gc_chip_card_one_rate_monthly numeric,
    gc_chip_card_gift_item_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_chip_card_loyalty_item_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_smart_card_printing numeric(15,4) DEFAULT NULL::numeric,
    gc_card_reorder_fee numeric,
    gc_loyalty_mgmt_database numeric(15,4) DEFAULT NULL::numeric,
    gc_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_application_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_equipment_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_misc_supplies numeric(15,4) DEFAULT NULL::numeric,
    gc_merch_prov_art_setup_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_news_provider_artwork_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_training_fee numeric(15,4) DEFAULT NULL::numeric,
    gc_plan character varying(8) DEFAULT NULL::character varying,
    gift_card_provider_id uuid
);


ALTER TABLE public.gift_cards OWNER TO axia;

--
-- Name: groups; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.groups (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    group_id_old character varying(36),
    group_description character varying(50) DEFAULT NULL::character varying,
    active boolean DEFAULT true NOT NULL
);


ALTER TABLE public.groups OWNER TO axia;

--
-- Name: imported_data_collections; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.imported_data_collections (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    month integer NOT NULL,
    year integer NOT NULL,
    gw_n1_id uuid,
    gw_n1_id_seq character varying(30),
    gw_n1_item_count integer,
    gw_n1_vol numeric,
    gw_n2_id uuid,
    gw_n2_id_seq character varying(30),
    gw_n2_item_count integer,
    gw_n2_vol numeric,
    pf_total_gw_item_count integer,
    pf_total_gw_vol numeric,
    devices_billed_count integer,
    pf_recurring_rev numeric,
    pf_recurring_item_rev numeric,
    pf_recurring_device_lic_rev numeric,
    pf_recurring_gw_rev numeric,
    pf_recurring_acct_rev numeric,
    pf_one_time_rev numeric,
    pf_per_item_fee numeric,
    pf_item_fee_total numeric,
    pf_monthly_fees numeric,
    pf_one_time_cost numeric,
    pf_rev_share numeric,
    pf_transactions integer,
    pf_actual_mo_vol numeric,
    acquiring_one_time_rev numeric,
    acquiring_one_time_cost numeric,
    ach_recurring_rev numeric,
    ach_recurring_gp numeric,
    is_usa_epay boolean,
    is_pf_gw boolean,
    mo_gateway_cost numeric,
    profit_loss_amount numeric,
    net_sales_count integer,
    net_sales numeric,
    gross_sales_count integer,
    gross_sales numeric,
    discount numeric,
    interchng_income numeric,
    interchng_expense numeric,
    other_income numeric,
    other_expense numeric,
    total_income numeric,
    total_expense numeric,
    gross_profit numeric,
    revised_gp numeric,
    total_income_minus_pf numeric,
    card_brand_expenses numeric,
    processor_expenses numeric,
    sponsor_cogs numeric,
    ithree_monthly_cogs numeric,
    sf_closed_date date,
    sf_opportunity_won boolean,
    sf_projected_accounts integer,
    sf_projected_devices integer,
    sf_projected_acq_tans integer,
    sf_projected_acq_vol numeric,
    sf_projected_pf_trans integer,
    sf_projected_pf_revenue numeric,
    sf_projected_pf_recurring_ach_revenue numeric,
    sf_projected_pf_recurring_ach_gp numeric,
    sf_support_cases_count integer
);


ALTER TABLE public.imported_data_collections OWNER TO axia;

--
-- Name: invoice_items; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.invoice_items (
    id uuid NOT NULL,
    merchant_ach_id uuid NOT NULL,
    merchant_ach_reason_id uuid,
    reason_other character varying(150),
    commissionable boolean DEFAULT false,
    taxable boolean DEFAULT false,
    non_taxable_reason_id uuid,
    amount numeric,
    tax_amount numeric
);


ALTER TABLE public.invoice_items OWNER TO axia;

--
-- Name: job_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.job_statuses (
    id uuid NOT NULL,
    name character varying(100) NOT NULL,
    rank integer NOT NULL
);


ALTER TABLE public.job_statuses OWNER TO axia;

--
-- Name: last_deposit_reports; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.last_deposit_reports (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_dba character varying(100) DEFAULT NULL::character varying,
    last_deposit_date date,
    user_id uuid NOT NULL,
    user_id_old character varying(36) DEFAULT NULL::character varying,
    monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    sales_num integer
);


ALTER TABLE public.last_deposit_reports OWNER TO axia;

--
-- Name: loggable_logs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.loggable_logs (
    id uuid NOT NULL,
    action character varying(150) NOT NULL,
    model character varying(150) NOT NULL,
    foreign_key character varying(150) NOT NULL,
    source_id character varying(150),
    content text NOT NULL,
    created timestamp without time zone,
    controller_action character varying(150)
);


ALTER TABLE public.loggable_logs OWNER TO axia;

--
-- Name: merchant_ach_app_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_ach_app_statuses (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    app_status_id_old character varying(36),
    app_status_description character varying(50) NOT NULL,
    rank integer,
    app_true_price numeric(15,4) DEFAULT NULL::numeric,
    app_rep_price numeric(15,4) DEFAULT NULL::numeric,
    enabled boolean DEFAULT true
);


ALTER TABLE public.merchant_ach_app_statuses OWNER TO axia;

--
-- Name: merchant_ach_billing_options; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_ach_billing_options (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    billing_option_id_old character varying(36),
    billing_option_description character varying(50) NOT NULL
);


ALTER TABLE public.merchant_ach_billing_options OWNER TO axia;

--
-- Name: merchant_ach_reasons; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_ach_reasons (
    id uuid NOT NULL,
    old_id character varying(255) DEFAULT NULL::character varying,
    reason character varying(255),
    "position" integer,
    enabled boolean DEFAULT true NOT NULL,
    accounting_report_col_alias character varying(100),
    non_taxable_reason_id uuid
);


ALTER TABLE public.merchant_ach_reasons OWNER TO axia;

--
-- Name: merchant_aches; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_aches (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    ach_seq_number_old integer,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    date_submitted date,
    date_completed date,
    reason character varying(100) DEFAULT NULL::character varying,
    credit_amount numeric(15,4) DEFAULT NULL::numeric,
    debit_amount numeric(15,4) DEFAULT NULL::numeric,
    reason_other character varying(100) DEFAULT NULL::character varying,
    status character varying(5) DEFAULT NULL::character varying,
    user_id uuid,
    user_id_old character varying(36) DEFAULT NULL::character varying,
    invoice_number bigint,
    app_status_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_ach_app_status_id uuid,
    billing_option_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_ach_billing_option_id uuid,
    depricated_commission_month character varying(8),
    tax numeric(15,4) DEFAULT NULL::numeric,
    merchant_ach_reason_id uuid,
    ach_date date,
    resubmit_date date,
    rep_bill_amount numeric,
    exact_shipping_amount numeric,
    general_shipping_amount numeric,
    ach_amount numeric,
    total_ach numeric,
    rejected boolean DEFAULT false NOT NULL,
    is_debit boolean,
    ach_not_collected boolean DEFAULT false NOT NULL,
    user_compensation_profile_id uuid,
    commission_month integer,
    commission_year integer,
    non_taxable_ach_amount numeric,
    tax_state_name character varying(2),
    tax_city_name character varying(50),
    tax_county_name character varying(100),
    tax_rate_state numeric,
    tax_rate_county numeric,
    tax_rate_city numeric,
    tax_rate_district numeric,
    tax_amount_state numeric,
    tax_amount_county numeric,
    tax_amount_city numeric,
    tax_amount_district numeric,
    ship_to_street character varying(100),
    ship_to_city character varying(50),
    ship_to_state character varying(2),
    ship_to_zip character varying(20),
    acctg_month integer,
    acctg_year integer,
    related_foreign_inv_number character varying(100)
);


ALTER TABLE public.merchant_aches OWNER TO axia;

--
-- Name: merchant_acquirers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_acquirers (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    acquirer_id_old character varying(36),
    acquirer character varying(21) NOT NULL,
    reference_only boolean DEFAULT false NOT NULL
);


ALTER TABLE public.merchant_acquirers OWNER TO axia;

--
-- Name: merchant_banks; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_banks (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    bank_routing_number character varying(255) DEFAULT NULL::character varying,
    bank_dda_number character varying(255) DEFAULT NULL::character varying,
    bank_name character varying(60) DEFAULT NULL::character varying,
    bank_routing_number_disp character varying(4) DEFAULT NULL::character varying,
    bank_dda_number_disp character varying(4) DEFAULT NULL::character varying,
    fees_routing_number character varying(255) DEFAULT NULL::character varying,
    fees_dda_number character varying(255) DEFAULT NULL::character varying,
    fees_routing_number_disp character varying(4) DEFAULT NULL::character varying,
    fees_dda_number_disp character varying(4) DEFAULT NULL::character varying
);


ALTER TABLE public.merchant_banks OWNER TO axia;

--
-- Name: merchant_bins; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_bins (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    bin_id_old character varying(36),
    bin character varying(21) NOT NULL
);


ALTER TABLE public.merchant_bins OWNER TO axia;

--
-- Name: merchant_cancellation_subreasons; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_cancellation_subreasons (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(65) NOT NULL,
    visible boolean DEFAULT true NOT NULL
);


ALTER TABLE public.merchant_cancellation_subreasons OWNER TO axia;

--
-- Name: merchant_cancellations; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_cancellations (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    date_submitted date,
    date_completed date,
    fee_charged numeric(15,4) DEFAULT NULL::numeric,
    reason text,
    status character varying(5) DEFAULT 'PEND'::character varying NOT NULL,
    axia_invoice_number bigint,
    date_inactive date,
    merchant_cancellation_subreason character varying(255) DEFAULT NULL::character varying,
    merchant_cancellation_subreason_id uuid,
    subreason_id_old character varying(36) DEFAULT NULL::character varying,
    exclude_from_attrition boolean DEFAULT false
);


ALTER TABLE public.merchant_cancellations OWNER TO axia;

--
-- Name: merchant_cancellations_histories; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_cancellations_histories (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    subreason_id uuid,
    date_submitted date,
    date_completed date,
    date_inactive date,
    date_reactivated date,
    fee_charged numeric,
    reason character varying(255),
    merchant_cancellation_subreason character varying(255),
    axia_invoice_number numeric
);


ALTER TABLE public.merchant_cancellations_histories OWNER TO axia;

--
-- Name: merchant_card_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_card_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    card_type_id uuid NOT NULL,
    card_type_old character varying(36)
);


ALTER TABLE public.merchant_card_types OWNER TO axia;

--
-- Name: merchant_changes; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_changes (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    change_id_old character varying(36),
    change_type_id uuid NOT NULL,
    change_type_old integer,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    status character varying(5) NOT NULL,
    date_entered date NOT NULL,
    time_entered time without time zone NOT NULL,
    date_approved date,
    time_approved time without time zone,
    change_data text,
    merchant_note_id uuid,
    merchant_note_id_old character varying(36) DEFAULT NULL::character varying,
    approved_by_user_id uuid,
    approved_by_old character varying(36) DEFAULT NULL::character varying,
    equipment_programming_id uuid,
    programming_id_old character varying(36)
);


ALTER TABLE public.merchant_changes OWNER TO axia;

--
-- Name: merchant_gateways_archives; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_gateways_archives (
    id uuid NOT NULL,
    gateway1_pricing_id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    gateway_id uuid,
    user_id uuid NOT NULL,
    month integer NOT NULL,
    year integer NOT NULL,
    referer_id uuid,
    reseller_id uuid,
    ref_p_type character varying(255) DEFAULT NULL::character varying,
    ref_p_value double precision,
    res_p_type character varying(255) DEFAULT NULL::character varying,
    res_p_value double precision,
    ref_p_pct double precision,
    res_p_pct double precision,
    r_rate_pct double precision,
    r_per_item_fee double precision,
    r_statement_fee double precision,
    m_rate_pct double precision,
    m_per_item double precision,
    m_statement double precision
);


ALTER TABLE public.merchant_gateways_archives OWNER TO axia;

--
-- Name: merchant_notes; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_notes (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_note_id_old character varying(36),
    note_type_id_old character varying(36),
    note_type_id uuid,
    user_id_old character varying(36),
    user_id uuid NOT NULL,
    merchant_id_old character varying(36),
    merchant_id uuid NOT NULL,
    note_date timestamp without time zone,
    note text,
    note_title character varying(200) DEFAULT NULL::character varying,
    general_status character varying(5) DEFAULT NULL::character varying,
    date_changed date,
    critical integer,
    note_sent integer,
    loggable_log_id uuid,
    resolved_date date,
    resolved_time time without time zone,
    change_id_old character varying(36) DEFAULT NULL::character varying,
    approved_by_user_id uuid
);


ALTER TABLE public.merchant_notes OWNER TO axia;

--
-- Name: merchant_owners; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_owners (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    owner_id_old character varying(36),
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    owner_social_sec_no character varying(255) DEFAULT NULL::character varying,
    owner_equity integer,
    owner_name character varying(100) DEFAULT NULL::character varying,
    owner_title character varying(40) DEFAULT NULL::character varying,
    owner_social_sec_no_disp character varying(4) DEFAULT NULL::character varying
);


ALTER TABLE public.merchant_owners OWNER TO axia;

--
-- Name: merchant_pcis; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_pcis (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    compliance_level integer DEFAULT 4 NOT NULL,
    saq_completed_date date,
    compliance_fee numeric(15,4) DEFAULT NULL::numeric,
    insurance_fee numeric(15,4) DEFAULT NULL::numeric,
    last_security_scan date,
    scanning_company character varying(255) DEFAULT NULL::character varying,
    pci_enabled boolean,
    saq_type text,
    cs_board_request_guid uuid,
    cs_cancel_request_guid uuid,
    cancelled_controlscan boolean DEFAULT false,
    cancelled_controlscan_date date
);


ALTER TABLE public.merchant_pcis OWNER TO axia;

--
-- Name: merchant_pricing_archives; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_pricing_archives (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    user_id uuid NOT NULL,
    acquirer_id uuid,
    provider_id uuid,
    products_services_type_id uuid NOT NULL,
    month integer NOT NULL,
    year integer NOT NULL,
    m_rate_pct numeric,
    m_per_item_fee numeric,
    m_discount_item_fee numeric,
    m_statement_fee numeric,
    bet_table_id uuid,
    bet_network_id uuid,
    months_processing integer,
    interchange_expense numeric,
    product_profit numeric,
    gateway_mid character varying(20),
    generic_product_mid character varying(20),
    bet_extra_pct numeric,
    original_m_rate_pct numeric
);


ALTER TABLE public.merchant_pricing_archives OWNER TO axia;

--
-- Name: merchant_pricings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_pricings (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    gateway_id uuid,
    mc_bet_table_id uuid,
    visa_bet_table_id uuid,
    ds_bet_table_id uuid,
    ds_processing_rate numeric,
    processing_rate numeric,
    mc_vi_auth numeric,
    ds_auth_fee numeric,
    billing_mc_vi_auth numeric,
    billing_discover_auth numeric,
    billing_amex_auth numeric,
    billing_debit_auth numeric,
    discount_item_fee numeric,
    amex_auth_fee numeric,
    aru_voice_auth_fee numeric,
    wireless_auth_fee numeric,
    wireless_auth_cost numeric,
    num_wireless_term integer,
    per_wireless_term_cost numeric,
    total_wireless_term_cost numeric,
    statement_fee numeric,
    min_month_process_fee numeric,
    ebt_auth_fee numeric,
    ebt_access_fee numeric,
    ebt_processing_rate numeric,
    gateway_access_fee numeric,
    wireless_access_fee numeric,
    debit_auth_fee numeric,
    debit_processing_rate numeric,
    debit_access_fee numeric,
    debit_acquirer_id uuid,
    annual_fee numeric,
    chargeback_fee numeric,
    amex_processing_rate numeric,
    amex_per_item_fee numeric,
    cr_gateway_fees_separate boolean,
    cr_gateway_rate_cost numeric,
    cr_gateway_item_cost numeric,
    cr_gateway_monthly_cost numeric,
    pyc_charge_to_merchant numeric,
    pyc_merchant_rebate numeric,
    syc_charge_to_merchant numeric,
    debit_discount_item_fee numeric,
    ebt_discount_item_fee numeric,
    ebt_acquirer_id uuid,
    features text,
    billing_ebt_auth numeric,
    ds_user_not_paid boolean DEFAULT false,
    db_bet_table_id uuid,
    ebt_bet_table_id uuid,
    amex_bet_table_id uuid,
    mc_acquirer_fee numeric DEFAULT 0
);


ALTER TABLE public.merchant_pricings OWNER TO axia;

--
-- Name: merchant_references; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_references (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_ref_seq_number_old integer,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    merchant_reference_type_id uuid,
    merchant_ref_type_old character varying(36),
    business_name character varying(100),
    person_name character varying(100) DEFAULT NULL::character varying,
    phone character varying(20) DEFAULT NULL::character varying
);


ALTER TABLE public.merchant_references OWNER TO axia;

--
-- Name: merchant_reject_lines; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_reject_lines (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    reject_id_old character varying(36),
    merchant_reject_id uuid NOT NULL,
    fee numeric(15,4) DEFAULT NULL::numeric,
    status_id_old character varying(36),
    merchant_reject_status_id uuid NOT NULL,
    status_date date,
    notes text
);


ALTER TABLE public.merchant_reject_lines OWNER TO axia;

--
-- Name: merchant_reject_recurrances; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_reject_recurrances (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(57) NOT NULL
);


ALTER TABLE public.merchant_reject_recurrances OWNER TO axia;

--
-- Name: merchant_reject_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_reject_statuses (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(57) NOT NULL,
    collected boolean,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.merchant_reject_statuses OWNER TO axia;

--
-- Name: merchant_reject_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_reject_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(57) NOT NULL
);


ALTER TABLE public.merchant_reject_types OWNER TO axia;

--
-- Name: merchant_rejects; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_rejects (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    trace character varying(30) NOT NULL,
    reject_date date NOT NULL,
    merchant_reject_type_id uuid NOT NULL,
    type_id_old character varying(36),
    code character varying(9) NOT NULL,
    amount numeric(15,4) DEFAULT 0 NOT NULL,
    merchant_reject_recurrance_id uuid NOT NULL,
    recurrance_id_old character varying(36),
    open boolean DEFAULT true NOT NULL,
    loss_axia numeric(15,4) DEFAULT NULL::numeric,
    loss_mgr1 numeric(15,4) DEFAULT NULL::numeric,
    loss_mgr2 numeric(15,4) DEFAULT NULL::numeric,
    loss_rep numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.merchant_rejects OWNER TO axia;

--
-- Name: merchant_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_types (
    id uuid NOT NULL,
    type_description character varying(100)
);


ALTER TABLE public.merchant_types OWNER TO axia;

--
-- Name: merchant_uw_final_approveds; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_uw_final_approveds (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_uw_final_approved_id_old character varying(36),
    name character varying(21) NOT NULL,
    active boolean DEFAULT true
);


ALTER TABLE public.merchant_uw_final_approveds OWNER TO axia;

--
-- Name: merchant_uw_final_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_uw_final_statuses (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(21) NOT NULL
);


ALTER TABLE public.merchant_uw_final_statuses OWNER TO axia;

--
-- Name: merchant_uw_volumes; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_uw_volumes (
    id uuid NOT NULL,
    merchant_uw_id uuid,
    merchant_id uuid NOT NULL,
    mc_vi_ds_risk_assessment numeric(15,2),
    mo_volume numeric(15,2),
    average_ticket numeric(15,2),
    max_transaction_amount numeric(15,2),
    sales integer,
    ds_volume numeric(15,2),
    pin_debit_volume numeric(15,2),
    pin_debit_avg_ticket numeric(15,2),
    amex_volume numeric(15,2),
    amex_avg_ticket numeric(15,2),
    card_present_swiped numeric(15,2),
    card_present_imprint numeric(15,2),
    card_not_present_keyed numeric(15,2),
    card_not_present_internet numeric(15,2),
    direct_to_consumer numeric(15,2),
    direct_to_business numeric(15,2),
    direct_to_government numeric(15,2),
    te_amex_number character varying(255),
    te_diners_club_number character varying(255),
    te_discover_number character varying(255),
    te_jcb_number character varying(255),
    te_amex_number_disp character varying(4),
    te_diners_club_number_disp character varying(4),
    te_discover_number_disp character varying(4),
    te_jcb_number_disp character varying(4),
    mc_volume numeric,
    visa_volume numeric,
    discount_frequency character varying(20) DEFAULT NULL::character varying,
    fees_collected_daily boolean DEFAULT false,
    next_day_funding boolean DEFAULT false
);


ALTER TABLE public.merchant_uw_volumes OWNER TO axia;

--
-- Name: merchant_uws; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchant_uws (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    tier_assignment integer,
    credit_pct numeric(15,4) DEFAULT NULL::numeric,
    chargeback_pct numeric(15,4) DEFAULT NULL::numeric,
    merchant_uw_final_status_id uuid,
    final_status_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_uw_final_approved_id uuid,
    final_approved_id_old character varying(36) DEFAULT NULL::character varying,
    final_date date,
    final_notes text,
    mcc character varying(255) DEFAULT NULL::character varying,
    expedited boolean NOT NULL,
    ebt_risk_assessment numeric,
    debit_risk_assessment numeric,
    sponsor_bank_id uuid,
    credit_score numeric,
    funding_delay_sales numeric,
    funding_delay_credits numeric,
    app_quantity_type character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.merchant_uws OWNER TO axia;

--
-- Name: merchants; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.merchants (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id_old character varying(36),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    merchant_mid character varying(20),
    merchant_name character varying(100) DEFAULT NULL::character varying,
    merchant_dba character varying(100) DEFAULT NULL::character varying,
    merchant_contact character varying(50) DEFAULT NULL::character varying,
    merchant_email character varying(50) DEFAULT NULL::character varying,
    merchant_ownership_type character varying(30) DEFAULT NULL::character varying,
    merchant_tin character varying(255) DEFAULT NULL::character varying,
    merchant_d_and_b character varying(255) DEFAULT NULL::character varying,
    inactive_date date,
    active_date date,
    referer_id uuid,
    ref_seq_number_old integer,
    network_id uuid,
    network_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_buslevel character varying(50) DEFAULT NULL::character varying,
    merchant_sic integer,
    entity_old character varying(36) DEFAULT NULL::character varying,
    entity_id uuid,
    group_id uuid,
    group_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_bustype character varying(100) DEFAULT NULL::character varying,
    merchant_url character varying(100) DEFAULT NULL::character varying,
    merchant_tin_disp character varying(4) DEFAULT NULL::character varying,
    merchant_d_and_b_disp character varying(4) DEFAULT NULL::character varying,
    active integer,
    cancellation_fee_id uuid,
    cancellation_fee_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_contact_position character varying(50) DEFAULT NULL::character varying,
    merchant_mail_contact character varying(50) DEFAULT NULL::character varying,
    reseller_id uuid,
    res_seq_number_old integer,
    merchant_bin_id uuid,
    merchant_bin_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_acquirer_id uuid,
    merchant_acquirer_id_old character varying(36) DEFAULT NULL::character varying,
    reporting_user character varying(65) DEFAULT NULL::character varying,
    merchant_ps_sold character varying(100) DEFAULT NULL::character varying,
    ref_p_type text,
    ref_p_value numeric(15,4) DEFAULT NULL::numeric,
    res_p_type text,
    res_p_value numeric(15,4) DEFAULT NULL::numeric,
    ref_p_pct integer,
    res_p_pct integer,
    onlineapp_application_id uuid,
    partner_id uuid,
    partner_id_old character varying(36) DEFAULT NULL::character varying,
    partner_exclude_volume boolean,
    aggregated boolean,
    cobranded_application_id integer,
    product_group_id uuid,
    original_acquirer_id uuid,
    bet_network_id uuid,
    back_end_network_id uuid,
    sm_user_id uuid,
    sm2_user_id uuid,
    brand_id uuid,
    womply_status_id uuid,
    womply_merchant_enabled boolean DEFAULT false NOT NULL,
    womply_customer boolean DEFAULT false NOT NULL,
    region_id uuid,
    subregion_id uuid,
    organization_id uuid,
    source_of_sale character varying(255),
    is_acquiring_only boolean,
    is_pf_only boolean,
    general_practice_type character varying(255),
    specific_practice_type character varying(255),
    client_id uuid,
    chargebacks_email character varying(50),
    merchant_type_id uuid
);


ALTER TABLE public.merchants OWNER TO axia;

--
-- Name: networks; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.networks (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    network_id_old character varying(36),
    network_description character varying(50) DEFAULT NULL::character varying,
    "position" integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.networks OWNER TO axia;

--
-- Name: non_taxable_reasons; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.non_taxable_reasons (
    id uuid NOT NULL,
    reason character varying(200)
);


ALTER TABLE public.non_taxable_reasons OWNER TO axia;

--
-- Name: note_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.note_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    note_type_id_old character varying(36),
    note_type_description character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.note_types OWNER TO axia;

--
-- Name: old_bet_tables_card_types_backup; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.old_bet_tables_card_types_backup (
    id uuid NOT NULL,
    bet_table_id uuid NOT NULL,
    card_type_id uuid NOT NULL
);


ALTER TABLE public.old_bet_tables_card_types_backup OWNER TO axia;

--
-- Name: old_bets_backup; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.old_bets_backup (
    id uuid NOT NULL,
    network_id uuid NOT NULL,
    user_id uuid NOT NULL,
    bet_tables_card_types_id uuid NOT NULL,
    bc_cost numeric DEFAULT 0 NOT NULL,
    bc_pi numeric DEFAULT 0 NOT NULL,
    csv_cost numeric DEFAULT 0 NOT NULL,
    csv_pi numeric DEFAULT 0 NOT NULL,
    add_pct numeric DEFAULT 0 NOT NULL
);


ALTER TABLE public.old_bets_backup OWNER TO axia;

--
-- Name: onlineapp_api_logs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_api_logs (
    id uuid NOT NULL,
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
-- Name: onlineapp_apips; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_apips (
    id integer NOT NULL,
    user_id integer,
    ip_address inet
);


ALTER TABLE public.onlineapp_apips OWNER TO axia;

--
-- Name: onlineapp_apips_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_apips_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_apips_id_seq OWNER TO axia;

--
-- Name: onlineapp_apips_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_apips_id_seq OWNED BY public.onlineapp_apips.id;


--
-- Name: onlineapp_applications; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_applications (
    id integer NOT NULL,
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
    tickler_id uuid,
    callback_url character varying(255) DEFAULT NULL::character varying,
    guid character varying(40) DEFAULT NULL::character varying,
    redirect_url character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_applications OWNER TO axia;

--
-- Name: onlineapp_applications_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_applications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_applications_id_seq OWNER TO axia;

--
-- Name: onlineapp_applications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_applications_id_seq OWNED BY public.onlineapp_applications.id;


--
-- Name: onlineapp_cobranded_application_aches; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_cobranded_application_aches (
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

CREATE SEQUENCE public.onlineapp_cobranded_application_aches_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobranded_application_aches_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_aches_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_cobranded_application_aches_id_seq OWNED BY public.onlineapp_cobranded_application_aches.id;


--
-- Name: onlineapp_cobranded_application_values; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_cobranded_application_values (
    id integer NOT NULL,
    cobranded_application_id integer NOT NULL,
    template_field_id integer NOT NULL,
    name character varying(255) NOT NULL,
    value text,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);


ALTER TABLE public.onlineapp_cobranded_application_values OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_values_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_cobranded_application_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobranded_application_values_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobranded_application_values_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_cobranded_application_values_id_seq OWNED BY public.onlineapp_cobranded_application_values.id;


--
-- Name: onlineapp_cobranded_applications; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_cobranded_applications (
    id integer NOT NULL,
    user_id integer,
    template_id integer,
    uuid uuid NOT NULL,
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

CREATE SEQUENCE public.onlineapp_cobranded_applications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobranded_applications_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobranded_applications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_cobranded_applications_id_seq OWNED BY public.onlineapp_cobranded_applications.id;


--
-- Name: onlineapp_cobrands; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_cobrands (
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

CREATE SEQUENCE public.onlineapp_cobrands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_cobrands_id_seq OWNER TO axia;

--
-- Name: onlineapp_cobrands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_cobrands_id_seq OWNED BY public.onlineapp_cobrands.id;


--
-- Name: onlineapp_coversheets; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_coversheets (
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

CREATE SEQUENCE public.onlineapp_coversheets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_coversheets_id_seq OWNER TO axia;

--
-- Name: onlineapp_coversheets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_coversheets_id_seq OWNED BY public.onlineapp_coversheets.id;


--
-- Name: onlineapp_email_timeline_subjects; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_email_timeline_subjects (
    id integer NOT NULL,
    subject character varying(40) DEFAULT NULL::character varying
);


ALTER TABLE public.onlineapp_email_timeline_subjects OWNER TO axia;

--
-- Name: onlineapp_email_timeline_subjects_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_email_timeline_subjects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_email_timeline_subjects_id_seq OWNER TO axia;

--
-- Name: onlineapp_email_timeline_subjects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_email_timeline_subjects_id_seq OWNED BY public.onlineapp_email_timeline_subjects.id;


--
-- Name: onlineapp_email_timelines; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_email_timelines (
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

CREATE SEQUENCE public.onlineapp_email_timelines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_email_timelines_id_seq OWNER TO axia;

--
-- Name: onlineapp_email_timelines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_email_timelines_id_seq OWNED BY public.onlineapp_email_timelines.id;


--
-- Name: onlineapp_epayments; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_epayments (
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

CREATE SEQUENCE public.onlineapp_epayments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_epayments_id_seq OWNER TO axia;

--
-- Name: onlineapp_epayments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_epayments_id_seq OWNED BY public.onlineapp_epayments.id;


--
-- Name: onlineapp_groups; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_groups (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp(6) without time zone,
    modified timestamp(6) without time zone
);


ALTER TABLE public.onlineapp_groups OWNER TO axia;

--
-- Name: onlineapp_groups_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_groups_id_seq OWNER TO axia;

--
-- Name: onlineapp_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_groups_id_seq OWNED BY public.onlineapp_groups.id;


--
-- Name: onlineapp_multipasses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_multipasses (
    id uuid NOT NULL,
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
-- Name: onlineapp_settings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_settings (
    key character varying(255) NOT NULL,
    value character varying(255),
    description text
);


ALTER TABLE public.onlineapp_settings OWNER TO axia;

--
-- Name: onlineapp_template_fields; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_template_fields (
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

CREATE SEQUENCE public.onlineapp_template_fields_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_template_fields_id_seq OWNER TO axia;

--
-- Name: onlineapp_template_fields_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_template_fields_id_seq OWNED BY public.onlineapp_template_fields.id;


--
-- Name: onlineapp_template_pages; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_template_pages (
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

CREATE SEQUENCE public.onlineapp_template_pages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_template_pages_id_seq OWNER TO axia;

--
-- Name: onlineapp_template_pages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_template_pages_id_seq OWNED BY public.onlineapp_template_pages.id;


--
-- Name: onlineapp_template_sections; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_template_sections (
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

CREATE SEQUENCE public.onlineapp_template_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_template_sections_id_seq OWNER TO axia;

--
-- Name: onlineapp_template_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_template_sections_id_seq OWNED BY public.onlineapp_template_sections.id;


--
-- Name: onlineapp_templates; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_templates (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    logo_position integer DEFAULT 3 NOT NULL,
    include_axia_logo boolean DEFAULT true NOT NULL,
    description text,
    cobrand_id integer,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    rightsignature_template_guid character varying(50) DEFAULT NULL::character varying,
    rightsignature_install_template_guid character varying(50) DEFAULT NULL::character varying,
    owner_equity_threshold integer
);


ALTER TABLE public.onlineapp_templates OWNER TO axia;

--
-- Name: onlineapp_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_templates_id_seq OWNER TO axia;

--
-- Name: onlineapp_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_templates_id_seq OWNED BY public.onlineapp_templates.id;


--
-- Name: onlineapp_users; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_users (
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

CREATE SEQUENCE public.onlineapp_users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_users_id_seq OWNER TO axia;

--
-- Name: onlineapp_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_users_id_seq OWNED BY public.onlineapp_users.id;


--
-- Name: onlineapp_users_managers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_users_managers (
    id character(36) NOT NULL,
    user_id integer,
    manager_id integer
);


ALTER TABLE public.onlineapp_users_managers OWNER TO axia;

--
-- Name: onlineapp_users_onlineapp_cobrands; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_users_onlineapp_cobrands (
    id integer NOT NULL,
    user_id integer,
    cobrand_id integer
);


ALTER TABLE public.onlineapp_users_onlineapp_cobrands OWNER TO axia;

--
-- Name: onlineapp_users_onlineapp_cobrands_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_users_onlineapp_cobrands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_users_onlineapp_cobrands_id_seq OWNER TO axia;

--
-- Name: onlineapp_users_onlineapp_cobrands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_users_onlineapp_cobrands_id_seq OWNED BY public.onlineapp_users_onlineapp_cobrands.id;


--
-- Name: onlineapp_users_onlineapp_templates; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.onlineapp_users_onlineapp_templates (
    id integer NOT NULL,
    user_id integer,
    template_id integer
);


ALTER TABLE public.onlineapp_users_onlineapp_templates OWNER TO axia;

--
-- Name: onlineapp_users_onlineapp_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.onlineapp_users_onlineapp_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.onlineapp_users_onlineapp_templates_id_seq OWNER TO axia;

--
-- Name: onlineapp_users_onlineapp_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.onlineapp_users_onlineapp_templates_id_seq OWNED BY public.onlineapp_users_onlineapp_templates.id;


--
-- Name: orderitem_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.orderitem_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    orderitem_type_id_old character varying(36),
    orderitem_type_description character varying(55) DEFAULT NULL::character varying
);


ALTER TABLE public.orderitem_types OWNER TO axia;

--
-- Name: orderitems; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.orderitems (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    order_id uuid NOT NULL,
    equipment_type_id uuid NOT NULL,
    equipment_item_description character varying(100) DEFAULT NULL::character varying,
    quantity integer,
    equipment_item_true_price numeric,
    equipment_item_rep_price numeric,
    equipment_item_id uuid,
    hardware_sn character varying(20) DEFAULT NULL::character varying,
    hardware_replacement_for character varying(20) DEFAULT NULL::character varying,
    item_tax numeric,
    item_ship_cost numeric,
    depricated_commission_month character varying(8),
    item_date_ordered date,
    orderitem_id uuid,
    warranty integer,
    warranty_id uuid,
    merchant_id uuid,
    item_merchant_id character varying(50) DEFAULT NULL::character varying,
    item_ship_type integer,
    shipping_type_item_id uuid,
    item_ach_seq integer,
    merchant_ach_id uuid,
    orderitem_type_id uuid,
    type_id uuid,
    commission_month integer,
    commission_year integer,
    equipment_item_partner_price numeric
);


ALTER TABLE public.orderitems OWNER TO axia;

--
-- Name: orderitems_replacements; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.orderitems_replacements (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    orderitem_id uuid NOT NULL,
    shipping_axia_to_merchant_id uuid,
    shipping_axia_to_merchant_cost double precision,
    shipping_merchant_to_vendor_id uuid,
    shipping_vendor_to_axia_id uuid,
    shipping_vendor_to_axia_cost double precision,
    ra_num integer,
    tracking_num_old integer,
    date_shipped_to_vendor date,
    date_arrived_from_vendor date,
    amount_billed_to_merchant double precision,
    tracking_num character varying(25) DEFAULT NULL::character varying,
    orderitem_replacement_id uuid,
    shipping_merchant_to_vendor_cos double precision
);


ALTER TABLE public.orderitems_replacements OWNER TO axia;

--
-- Name: orders; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.orders (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    order_id_old character varying(36),
    status character varying(5) NOT NULL,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    date_ordered date,
    date_paid date,
    merchant_id uuid,
    merchant_id_old character varying(36) DEFAULT NULL::character varying,
    shipping_cost numeric(15,4) DEFAULT NULL::numeric,
    tax numeric(15,4) DEFAULT NULL::numeric,
    ship_to character varying(50) DEFAULT NULL::character varying,
    tracking_number character varying(50) DEFAULT NULL::character varying,
    notes text,
    invoice_number integer,
    shipping_type_id uuid,
    shipping_type_old integer,
    display_id uuid,
    merchant_ach_id uuid,
    ach_seq_number_old integer,
    depricated_commission_month character varying(8),
    vendor_id uuid,
    vendor_id_old character varying(36) DEFAULT NULL::character varying,
    add_item_tax integer,
    commission_month_nocharge integer,
    commission_month integer,
    commission_year integer
);


ALTER TABLE public.orders OWNER TO axia;

--
-- Name: organizations; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.organizations (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    name character varying(100) NOT NULL,
    active boolean DEFAULT true
);


ALTER TABLE public.organizations OWNER TO axia;

--
-- Name: original_acquirers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.original_acquirers (
    id uuid NOT NULL,
    acquirer character varying(50) NOT NULL
);


ALTER TABLE public.original_acquirers OWNER TO axia;

--
-- Name: partners; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.partners (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    partner_id_old character varying(36),
    partner_name character varying(255) NOT NULL,
    active integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.partners OWNER TO axia;

--
-- Name: payment_fusion_rep_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.payment_fusion_rep_costs (
    id uuid NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    rep_monthly_cost numeric,
    rep_per_item numeric,
    standard_device_cost numeric,
    vp2pe_device_cost numeric,
    pfcc_device_cost numeric,
    vp2pe_pfcc_device_cost numeric
);


ALTER TABLE public.payment_fusion_rep_costs OWNER TO axia;

--
-- Name: payment_fusions; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.payment_fusions (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    generic_product_mid character varying(20),
    account_fee numeric,
    rate numeric,
    monthly_total numeric,
    per_item_fee numeric,
    other_features text,
    standard_num_devices integer,
    standard_device_fee numeric,
    vp2pe_num_devices integer,
    vp2pe_device_fee numeric,
    pfcc_num_devices integer,
    pfcc_device_fee numeric,
    vp2pe_pfcc_num_devices integer,
    vp2pe_pfcc_device_fee numeric,
    is_hw_as_srvc boolean
);


ALTER TABLE public.payment_fusions OWNER TO axia;

--
-- Name: payment_fusions_product_features; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.payment_fusions_product_features (
    id uuid NOT NULL,
    payment_fusion_id uuid NOT NULL,
    product_feature_id uuid NOT NULL
);


ALTER TABLE public.payment_fusions_product_features OWNER TO axia;

--
-- Name: pci_billing_histories; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.pci_billing_histories (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    pci_billing_type_id uuid NOT NULL,
    saq_merchant_id uuid NOT NULL,
    billing_date date,
    date_change date NOT NULL,
    operation character varying(25) NOT NULL
);


ALTER TABLE public.pci_billing_histories OWNER TO axia;

--
-- Name: pci_billing_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.pci_billing_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    name character varying(255)
);


ALTER TABLE public.pci_billing_types OWNER TO axia;

--
-- Name: pci_billings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.pci_billings (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    pci_billing_type_id uuid NOT NULL,
    saq_merchant_id uuid NOT NULL,
    billing_date date
);


ALTER TABLE public.pci_billings OWNER TO axia;

--
-- Name: pci_compliance_date_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.pci_compliance_date_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.pci_compliance_date_types OWNER TO axia;

--
-- Name: pci_compliance_status_logs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.pci_compliance_status_logs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    pci_compliance_date_type_id uuid,
    pci_compliance_date_type_id_old character varying(36) DEFAULT NULL::character varying,
    saq_merchant_id uuid,
    saq_merchant_id_old character varying(36) DEFAULT NULL::character varying,
    date_complete date,
    date_change timestamp without time zone,
    operation character varying(25) DEFAULT NULL::character varying
);


ALTER TABLE public.pci_compliance_status_logs OWNER TO axia;

--
-- Name: pci_compliances; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.pci_compliances (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    pci_compliance_date_type_id uuid NOT NULL,
    pci_compliance_date_type_id_old character varying(36),
    saq_merchant_id uuid NOT NULL,
    saq_merchant_id_old character varying(36),
    date_complete date NOT NULL
);


ALTER TABLE public.pci_compliances OWNER TO axia;

--
-- Name: permission_caches; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.permission_caches (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    permission_id uuid NOT NULL,
    permission_name character varying(200) NOT NULL
);


ALTER TABLE public.permission_caches OWNER TO axia;

--
-- Name: permission_constraints; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.permission_constraints (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    permission_id uuid NOT NULL
);


ALTER TABLE public.permission_constraints OWNER TO axia;

--
-- Name: permissions; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.permissions (
    id uuid NOT NULL,
    name character varying(200) NOT NULL,
    description character varying(200) NOT NULL
);


ALTER TABLE public.permissions OWNER TO axia;

--
-- Name: permissions_roles; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.permissions_roles (
    id uuid NOT NULL,
    role_id uuid NOT NULL,
    permission_id uuid NOT NULL
);


ALTER TABLE public.permissions_roles OWNER TO axia;

--
-- Name: posts; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.posts (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    title character varying(255) NOT NULL,
    body character varying(255) NOT NULL,
    published_month integer,
    published_year integer,
    published_fulldate date
);


ALTER TABLE public.posts OWNER TO axia;

--
-- Name: pricing_matrices; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.pricing_matrices (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    matrix_id_old character varying(36),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    user_type_id uuid NOT NULL,
    user_type_old character varying(36),
    matrix_profit_perc numeric(15,4) DEFAULT NULL::numeric,
    matrix_new_monthly_volume numeric(15,4) DEFAULT NULL::numeric,
    matrix_new_monthly_profit numeric(15,4) DEFAULT NULL::numeric,
    matrix_view_conjunction uuid,
    matrix_total_volume numeric(15,4) DEFAULT NULL::numeric,
    matrix_total_accounts numeric(15,4) DEFAULT NULL::numeric,
    matrix_total_profit numeric(15,4) DEFAULT NULL::numeric,
    matrix_draw numeric(15,4) DEFAULT NULL::numeric,
    matrix_new_accounts_min numeric(15,4) DEFAULT NULL::numeric,
    matrix_new_accounts_max numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.pricing_matrices OWNER TO axia;

--
-- Name: product_categories; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.product_categories (
    id uuid NOT NULL,
    category_name character varying(100) NOT NULL
);


ALTER TABLE public.product_categories OWNER TO axia;

--
-- Name: product_features; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.product_features (
    id uuid NOT NULL,
    products_services_type_id uuid,
    feature_name character varying(255) NOT NULL
);


ALTER TABLE public.product_features OWNER TO axia;

--
-- Name: product_groups; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.product_groups (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    name text
);


ALTER TABLE public.product_groups OWNER TO axia;

--
-- Name: product_settings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.product_settings (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    products_services_type_id uuid NOT NULL,
    product_feature_id uuid,
    rate numeric DEFAULT 0,
    monthly_fee numeric DEFAULT 0,
    monthly_total numeric DEFAULT 0,
    per_item_fee numeric DEFAULT 0,
    gral_fee numeric DEFAULT 0,
    gral_fee_multiplier integer,
    generic_product_mid character varying(20)
);


ALTER TABLE public.product_settings OWNER TO axia;

--
-- Name: products_and_services; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.products_and_services (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    products_services_type_id uuid NOT NULL,
    products_services_type_id_old character varying(36)
);


ALTER TABLE public.products_and_services OWNER TO axia;

--
-- Name: products_services_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.products_services_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    products_services_type_id_old character varying(36),
    products_services_description character varying(50) DEFAULT NULL::character varying,
    products_services_rppp boolean DEFAULT true,
    is_active boolean DEFAULT true NOT NULL,
    class_identifier character varying(10) DEFAULT NULL::character varying,
    custom_labels text,
    is_legacy boolean,
    product_category_id uuid NOT NULL
);


ALTER TABLE public.products_services_types OWNER TO axia;

--
-- Name: profit_projections; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.profit_projections (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    products_services_type_id uuid NOT NULL,
    rep_gross_profit numeric,
    rep_profit_amount numeric,
    axia_profit_amount numeric
);


ALTER TABLE public.profit_projections OWNER TO axia;

--
-- Name: profitability_reports; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.profitability_reports (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    year integer,
    month integer,
    net_sales_vol numeric,
    net_sales_item_count integer,
    gross_sales_vol numeric,
    gross_sales_item_count integer,
    card_brand_cogs numeric,
    processor_cogs numeric,
    sponsor_bank_cogs numeric,
    ithree_monthly_cogs numeric,
    axia_net_income numeric,
    axia_gross_profit numeric,
    axia_net_profit numeric,
    cost_of_goods_sold numeric,
    total_income numeric,
    total_residual_comp numeric
);


ALTER TABLE public.profitability_reports OWNER TO axia;

--
-- Name: rate_structures; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.rate_structures (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    bet_table_id uuid NOT NULL,
    structure_name character varying(50) NOT NULL,
    qual_exemptions character varying(150)
);


ALTER TABLE public.rate_structures OWNER TO axia;

--
-- Name: referer_products_services_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.referer_products_services_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    referer_id uuid NOT NULL,
    ref_seq_number_old integer,
    products_services_type_id uuid NOT NULL,
    products_services_type_id_old character varying(36)
);


ALTER TABLE public.referer_products_services_xrefs OWNER TO axia;

--
-- Name: referers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.referers (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    ref_seq_number_old integer,
    ref_name character varying(50) DEFAULT NULL::character varying,
    ref_ref_perc numeric(15,4) DEFAULT NULL::numeric,
    active integer,
    ref_username character varying(50) DEFAULT NULL::character varying,
    ref_password character varying(50) DEFAULT NULL::character varying,
    ref_residuals boolean NOT NULL,
    ref_commissions boolean NOT NULL,
    is_referer boolean DEFAULT true NOT NULL
);


ALTER TABLE public.referers OWNER TO axia;

--
-- Name: referers_bets; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.referers_bets (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    referers_bet_id_old character varying(36),
    referer_id uuid NOT NULL,
    ref_seq_number_old integer,
    bet_id uuid NOT NULL,
    bet_code_old character varying(36),
    pct numeric(15,4) DEFAULT 0 NOT NULL
);


ALTER TABLE public.referers_bets OWNER TO axia;

--
-- Name: regions; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.regions (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    organization_id uuid NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.regions OWNER TO axia;

--
-- Name: rep_cost_structures; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.rep_cost_structures (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    debit_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    debit_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    gift_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    gift_magstripe_item_fee numeric(15,4) DEFAULT NULL::numeric,
    gift_magstripe_loyalty_fee numeric(15,4) DEFAULT NULL::numeric,
    gift_chipcard_item_fee numeric(15,4) DEFAULT NULL::numeric,
    gift_chipcard_loyalty_fee numeric(15,4) DEFAULT NULL::numeric,
    gift_chipcard_onerate_fee numeric(15,4) DEFAULT NULL::numeric,
    cg_volume numeric(15,4) DEFAULT NULL::numeric,
    vc_web_based_rate numeric(15,4) DEFAULT NULL::numeric,
    vc_web_based_pi numeric(15,4) DEFAULT NULL::numeric,
    vc_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    vc_gateway_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_merchant_based numeric(15,4) DEFAULT NULL::numeric,
    ach_file_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_eft_ccd_w numeric,
    ach_eft_ccd_nw numeric,
    ach_eft_ppd_nw numeric(15,4) DEFAULT NULL::numeric,
    ach_eft_ppd_w numeric(15,4) DEFAULT NULL::numeric,
    ach_eft_rck numeric(15,4) DEFAULT NULL::numeric,
    ach_reject_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    ach_secure_gateway_eft numeric(15,4) DEFAULT NULL::numeric,
    ebt_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    ebt_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    main_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    main_vrt_term_gwy_fee numeric(15,4) DEFAULT NULL::numeric,
    vc_web_based_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    vc_web_based_gateway_fee numeric(15,4) DEFAULT NULL::numeric,
    te_transaction_fee numeric(15,4) DEFAULT NULL::numeric,
    af_annual_fee numeric(15,4) DEFAULT NULL::numeric,
    multiple numeric(15,4) DEFAULT NULL::numeric,
    auth_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    auth_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    debit_rate_rep_cost_pct numeric(15,4) DEFAULT NULL::numeric,
    ach_rate numeric(15,4) DEFAULT NULL::numeric,
    gw_rate numeric(15,4) DEFAULT NULL::numeric,
    gw_per_item numeric(15,4) DEFAULT NULL::numeric,
    debit_per_item_fee_tsys numeric(15,4) DEFAULT NULL::numeric,
    debit_rate_rep_cost_pct_tsys numeric(15,4) DEFAULT NULL::numeric,
    debit_per_item_fee_tsys_new numeric(15,4) DEFAULT NULL::numeric,
    debit_rate_rep_cost_pct_tsys_new numeric(15,4) DEFAULT NULL::numeric,
    discover_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    debit_per_item_fee_directconnect numeric(15,4) DEFAULT NULL::numeric,
    debit_rate_rep_cost_pct_directconnect numeric(15,4) DEFAULT NULL::numeric,
    debit_per_item_fee_sagepti numeric(15,4) DEFAULT NULL::numeric,
    debit_rate_rep_cost_pct_sagepti numeric(15,4) DEFAULT NULL::numeric,
    tg_rate numeric(15,4) DEFAULT NULL::numeric,
    tg_per_item numeric(15,4) DEFAULT NULL::numeric,
    wp_rate numeric(15,4) DEFAULT NULL::numeric,
    wp_per_item numeric(15,4) DEFAULT NULL::numeric,
    tg_statement_fee numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.rep_cost_structures OWNER TO axia;

--
-- Name: rep_monthly_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.rep_monthly_costs (
    id uuid NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    bet_network_id uuid NOT NULL,
    credit_cost numeric,
    debit_cost numeric,
    ebt_cost numeric
);


ALTER TABLE public.rep_monthly_costs OWNER TO axia;

--
-- Name: rep_partner_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.rep_partner_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    partner_id uuid NOT NULL,
    partner_id_old character varying(36),
    mgr1_id uuid,
    mgr1_id_old character varying(36) DEFAULT NULL::character varying,
    mgr2_id uuid,
    mgr2_id_old character varying(36) DEFAULT NULL::character varying,
    profit_pct numeric(15,4) DEFAULT NULL::numeric,
    multiple numeric(4,2) DEFAULT NULL::numeric,
    mgr1_profit_pct numeric(15,4) DEFAULT NULL::numeric,
    mgr2_profit_pct numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.rep_partner_xrefs OWNER TO axia;

--
-- Name: rep_product_profit_pcts; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.rep_product_profit_pcts (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    products_services_type_id uuid NOT NULL,
    products_services_type_id_old character varying(36),
    pct numeric(15,4),
    multiple numeric(15,4),
    pct_gross numeric(15,4),
    do_not_display boolean
);


ALTER TABLE public.rep_product_profit_pcts OWNER TO axia;

--
-- Name: rep_product_settings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.rep_product_settings (
    id uuid NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    products_services_type_id uuid NOT NULL,
    rep_monthly_cost numeric(6,3) DEFAULT 0,
    rep_per_item numeric(6,3) DEFAULT 0,
    provider_device_cost numeric(6,3) DEFAULT 0
);


ALTER TABLE public.rep_product_settings OWNER TO axia;

--
-- Name: residual_parameter_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_parameter_types (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    "order" integer NOT NULL,
    type integer NOT NULL,
    value_type integer NOT NULL,
    is_multiple integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.residual_parameter_types OWNER TO axia;

--
-- Name: residual_parameters; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_parameters (
    id uuid NOT NULL,
    residual_parameter_type_id uuid,
    products_services_type_id uuid,
    associated_user_id uuid,
    type character varying(255) DEFAULT NULL::character varying,
    value numeric,
    is_multiple integer DEFAULT 0 NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone,
    tier integer DEFAULT 1 NOT NULL,
    user_compensation_profile_id uuid
);


ALTER TABLE public.residual_parameters OWNER TO axia;

--
-- Name: residual_pricings; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_pricings (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    products_services_type_id uuid NOT NULL,
    products_services_type_id_old character varying(36),
    user_id uuid NOT NULL,
    user_id_old uuid,
    r_network character varying(50) DEFAULT NULL::character varying,
    r_month numeric(15,4) DEFAULT NULL::numeric,
    r_year numeric(15,4) DEFAULT NULL::numeric,
    r_rate_pct numeric(15,4) DEFAULT NULL::numeric,
    r_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    r_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    m_rate_pct numeric(15,4) DEFAULT NULL::numeric,
    m_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    m_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    refer_profit_pct numeric(15,4) DEFAULT NULL::numeric,
    ref_seq_number_old integer,
    referer_id uuid,
    m_hidden_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    m_hidden_per_item_fee_cross numeric(15,4) DEFAULT NULL::numeric,
    m_eft_secure_flag integer,
    m_gc_plan character varying(8) DEFAULT NULL::character varying,
    m_pos_partner_flag integer,
    bet_table_id uuid,
    bet_code_old character varying(36) DEFAULT NULL::character varying,
    bet_extra_pct numeric(15,4) DEFAULT NULL::numeric,
    pct_volume boolean,
    res_profit_pct numeric(15,4) DEFAULT NULL::numeric,
    reseller_id uuid,
    res_seq_number_old integer,
    m_micros_ip_flag integer,
    m_micros_dialup_flag integer,
    m_micros_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    m_wireless_flag integer,
    m_wireless_terminals integer,
    r_usaepay_flag integer,
    r_epay_retail_flag integer,
    r_usaepay_gtwy_cost numeric(15,4) DEFAULT NULL::numeric,
    r_usaepay_gtwy_add_cost numeric(15,4) DEFAULT NULL::numeric,
    m_tgate_flag integer,
    m_petro_flag integer,
    ref_p_type text,
    ref_p_value numeric(15,4) DEFAULT NULL::numeric,
    res_p_type text,
    res_p_value numeric(15,4) DEFAULT NULL::numeric,
    r_risk_assessment numeric(15,4) DEFAULT NULL::numeric,
    ref_p_pct integer,
    res_p_pct integer
);


ALTER TABLE public.residual_pricings OWNER TO axia;

--
-- Name: residual_product_controls; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_product_controls (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    product_service_type_id uuid NOT NULL,
    do_no_display boolean DEFAULT false,
    tier_product boolean DEFAULT false,
    enabled_for_rep boolean DEFAULT false,
    rep_params_gross_profit_tsys numeric DEFAULT 0,
    rep_params_gross_profit_sage numeric DEFAULT 0,
    rep_params_gross_profit_dc numeric DEFAULT 0,
    manager1_params_gross_profit_tsys numeric DEFAULT 0,
    manager1_params_gross_profit_sage numeric DEFAULT 0,
    manager1_params_gross_profit_dc numeric DEFAULT 0,
    manager2_params_gross_profit_tsys numeric DEFAULT 0,
    manager2_params_gross_profit_sage numeric DEFAULT 0,
    manager2_params_gross_profit_dc numeric DEFAULT 0,
    override_rep_percentage numeric DEFAULT 0,
    override_multiple numeric DEFAULT 0,
    override_manager1 numeric DEFAULT 0,
    override_manager2 numeric DEFAULT 0
);


ALTER TABLE public.residual_product_controls OWNER TO axia;

--
-- Name: residual_reports; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_reports (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    products_services_type_id uuid NOT NULL,
    products_services_type_id_old character varying(36),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    r_network uuid,
    r_month numeric(15,4) DEFAULT NULL::numeric,
    r_year numeric(15,4) DEFAULT NULL::numeric,
    r_rate_pct numeric(15,4) DEFAULT NULL::numeric,
    r_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    r_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    r_profit_pct numeric(15,4) DEFAULT NULL::numeric,
    r_profit_amount numeric(15,4) DEFAULT NULL::numeric,
    m_rate_pct numeric(15,4) DEFAULT NULL::numeric,
    m_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    refer_profit_pct numeric(15,4) DEFAULT NULL::numeric,
    refer_profit_amount numeric(15,4) DEFAULT NULL::numeric,
    ref_seq_number_old integer,
    referer_id uuid,
    status character varying(5) DEFAULT NULL::character varying,
    m_statement_fee numeric(15,4) DEFAULT NULL::numeric,
    r_avg_ticket numeric(15,4) DEFAULT NULL::numeric,
    r_items numeric(15,4) DEFAULT NULL::numeric,
    r_volume numeric(15,4) DEFAULT NULL::numeric,
    total_profit numeric(15,4) DEFAULT NULL::numeric,
    manager_id uuid,
    manager_id_old character varying(36) DEFAULT NULL::character varying,
    manager_profit_pct numeric(15,4) DEFAULT NULL::numeric,
    manager_profit_amount numeric(15,4) DEFAULT NULL::numeric,
    bet_table_id uuid,
    bet_code_old character varying(36) DEFAULT NULL::character varying,
    bet_extra_pct numeric(15,4) DEFAULT NULL::numeric,
    res_profit_pct numeric(15,4) DEFAULT NULL::numeric,
    res_profit_amount numeric(15,4) DEFAULT NULL::numeric,
    reseller_id uuid,
    res_seq_number_old integer,
    manager_id_secondary uuid,
    manager_id_secondary_old character varying(36) DEFAULT NULL::character varying,
    manager_profit_pct_secondary numeric(15,4) DEFAULT NULL::numeric,
    manager_profit_amount_secondary numeric(15,4) DEFAULT NULL::numeric,
    ref_p_type text,
    ref_p_value numeric(15,4) DEFAULT NULL::numeric,
    res_p_type text,
    res_p_value numeric(15,4) DEFAULT NULL::numeric,
    ref_p_pct integer,
    res_p_pct integer,
    partner_id uuid,
    partner_id_old character varying(36) DEFAULT NULL::character varying,
    partner_exclude_volume boolean,
    partner_profit_pct numeric(15,4),
    partner_profit_amount numeric(15,4),
    partner_rep_profit_pct numeric(15,4),
    partner_rep_profit_amount numeric(15,4),
    partner_rate numeric(15,4) DEFAULT 0,
    partner_per_item_fee numeric(15,4) DEFAULT 0,
    partner_statement_fee numeric(15,4) DEFAULT 0,
    partner_gross_profit numeric(15,4) DEFAULT 0,
    partner_pct_of_gross numeric(15,4) DEFAULT 0,
    referrer_rate numeric(15,4) DEFAULT 0,
    referrer_per_item_fee numeric(15,4) DEFAULT 0,
    referrer_statement_fee numeric(15,4) DEFAULT 0,
    referrer_gross_profit numeric(15,4) DEFAULT 0,
    referrer_pct_of_gross numeric(15,4) DEFAULT 0,
    reseller_rate numeric(15,4) DEFAULT 0,
    reseller_per_item_fee numeric(15,4) DEFAULT 0,
    reseller_statement_fee numeric(15,4) DEFAULT 0,
    reseller_gross_profit numeric(15,4) DEFAULT 0,
    reseller_pct_of_gross numeric(15,4) DEFAULT 0,
    merchant_state character varying(2) DEFAULT NULL::character varying,
    rep_gross_profit numeric(15,4) DEFAULT 0,
    rep_pct_of_gross numeric(15,4) DEFAULT 0,
    partner_rep_rate numeric(15,4) DEFAULT 0,
    partner_rep_per_item_fee numeric(15,4) DEFAULT 0,
    partner_rep_statement_fee numeric(15,4) DEFAULT 0,
    partner_rep_gross_profit numeric(15,4) DEFAULT 0,
    partner_rep_pct_of_gross numeric(15,4) DEFAULT 0,
    manager_rate numeric(15,4) DEFAULT 0,
    manager_per_item_fee numeric(15,4) DEFAULT 0,
    manager_statement_fee numeric(15,4) DEFAULT 0,
    manager_gross_profit numeric(15,4) DEFAULT 0,
    manager2_rate numeric(15,4) DEFAULT 0,
    manager2_per_item_fee numeric(15,4) DEFAULT 0,
    manager2_statement_fee numeric(15,4) DEFAULT 0,
    manager2_gross_profit numeric(15,4) DEFAULT 0,
    manager2_pct_of_gross numeric(15,4) DEFAULT 0,
    manager_pct_of_gross numeric(15,4) DEFAULT 0,
    profit_pct numeric,
    referer_rate numeric,
    referer_per_item_fee numeric,
    referer_statement_fee numeric,
    referer_gross_profit numeric,
    referer_pct_of_gross numeric,
    referer_profit_pct numeric,
    referer_profit_amount numeric,
    reseller_profit_pct numeric,
    reseller_profit_amount numeric,
    original_m_rate_pct numeric
);


ALTER TABLE public.residual_reports OWNER TO axia;

--
-- Name: residual_time_factors; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_time_factors (
    id uuid NOT NULL,
    tier1_begin_month integer DEFAULT 0 NOT NULL,
    tier1_end_month integer DEFAULT 0 NOT NULL,
    tier2_begin_month integer DEFAULT 0 NOT NULL,
    tier2_end_month integer DEFAULT 0 NOT NULL,
    tier3_begin_month integer DEFAULT 0 NOT NULL,
    tier3_end_month integer DEFAULT 0 NOT NULL,
    tier4_begin_month integer DEFAULT 0 NOT NULL,
    tier4_end_month integer DEFAULT 0 NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone,
    user_compensation_profile_id uuid
);


ALTER TABLE public.residual_time_factors OWNER TO axia;

--
-- Name: residual_time_parameters; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_time_parameters (
    id uuid NOT NULL,
    residual_parameter_type_id uuid,
    products_services_type_id uuid,
    associated_user_id uuid,
    type character varying(255) DEFAULT NULL::character varying,
    value numeric,
    is_multiple integer DEFAULT 0 NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone,
    tier integer DEFAULT 1 NOT NULL,
    user_compensation_profile_id uuid
);


ALTER TABLE public.residual_time_parameters OWNER TO axia;

--
-- Name: residual_volume_tiers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.residual_volume_tiers (
    id uuid NOT NULL,
    product_service_type_id uuid,
    tier1_minimum_volume numeric DEFAULT 0,
    tier1_minimum_gp numeric,
    tier1_maximum_volume numeric,
    tier1_maximum_gp numeric DEFAULT 0,
    tier2_minimum_volume numeric DEFAULT 0,
    tier2_minimum_gp numeric,
    tier2_maximum_volume numeric DEFAULT 0,
    tier2_maximum_gp numeric DEFAULT 0,
    tier3_minimum_volume numeric DEFAULT 0,
    tier3_minimum_gp numeric,
    tier3_maximum_volume numeric DEFAULT 0,
    tier3_maximum_gp numeric DEFAULT 0,
    tier4_minimum_volume numeric DEFAULT 0,
    tier4_minimum_gp numeric,
    tier4_maximum_volume numeric DEFAULT 0,
    tier4_maximum_gp numeric DEFAULT 0,
    created timestamp without time zone,
    modified timestamp without time zone,
    user_compensation_profile_id uuid
);


ALTER TABLE public.residual_volume_tiers OWNER TO axia;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.roles (
    id uuid NOT NULL,
    parent_id uuid,
    name character varying(50) NOT NULL,
    lft integer NOT NULL,
    rght integer NOT NULL
);


ALTER TABLE public.roles OWNER TO axia;

--
-- Name: sales_goal_archives; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.sales_goal_archives (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    goal_month integer,
    goal_year integer,
    goal_accounts integer,
    goal_volume numeric(15,4) DEFAULT NULL::numeric,
    goal_profits numeric(15,4) DEFAULT NULL::numeric,
    actual_accounts numeric(15,4) DEFAULT NULL::numeric,
    actual_volume numeric(15,4) DEFAULT NULL::numeric,
    actual_profits numeric(15,4) DEFAULT NULL::numeric,
    goal_statements numeric(15,4) DEFAULT NULL::numeric,
    goal_calls numeric(15,4) DEFAULT NULL::numeric,
    actual_statements numeric(15,4) DEFAULT NULL::numeric,
    actual_calls numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.sales_goal_archives OWNER TO axia;

--
-- Name: sales_goals; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.sales_goals (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    goal_accounts numeric(15,4) DEFAULT NULL::numeric,
    goal_volume numeric(15,4) DEFAULT NULL::numeric,
    goal_profits numeric(15,4) DEFAULT NULL::numeric,
    goal_statements numeric(15,4) DEFAULT NULL::numeric,
    goal_calls numeric(15,4) DEFAULT NULL::numeric,
    goal_month integer DEFAULT 1 NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.sales_goals OWNER TO axia;

--
-- Name: saq_answers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_answers (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    saq_merchant_survey_xref_id uuid NOT NULL,
    saq_merchant_survey_xref_id_old uuid,
    saq_survey_question_xref_id uuid NOT NULL,
    saq_survey_question_xref_id_old character varying(36),
    answer boolean NOT NULL,
    date timestamp without time zone NOT NULL
);


ALTER TABLE public.saq_answers OWNER TO axia;

--
-- Name: saq_control_scan_unboardeds; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_control_scan_unboardeds (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36) DEFAULT NULL::character varying,
    date_unboarded date
);


ALTER TABLE public.saq_control_scan_unboardeds OWNER TO axia;

--
-- Name: saq_control_scans; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_control_scans (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    saq_type character varying(36) DEFAULT NULL::character varying,
    first_scan_date date,
    first_questionnaire_date date,
    scan_status text,
    questionnaire_status text,
    creation_date date,
    dba character varying(80) DEFAULT NULL::character varying,
    quarterly_scan_fee double precision DEFAULT (11.95)::double precision NOT NULL,
    sua timestamp without time zone,
    pci_compliance character varying(3) DEFAULT NULL::character varying,
    offline_compliance character varying(3) DEFAULT NULL::character varying,
    compliant_date date,
    host_count integer,
    submids text
);


ALTER TABLE public.saq_control_scans OWNER TO axia;

--
-- Name: saq_merchant_pci_email_sents; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_merchant_pci_email_sents (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    saq_merchant_id uuid NOT NULL,
    saq_merchant_id_old character varying(36),
    saq_merchant_pci_email_id uuid NOT NULL,
    saq_merchant_pci_email_id_old character varying(36),
    date_sent timestamp without time zone NOT NULL
);


ALTER TABLE public.saq_merchant_pci_email_sents OWNER TO axia;

--
-- Name: saq_merchant_pci_emails; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_merchant_pci_emails (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    priority integer NOT NULL,
    "interval" integer NOT NULL,
    title character varying(255) NOT NULL,
    filename_prefix character varying(255) NOT NULL,
    visible boolean DEFAULT true NOT NULL
);


ALTER TABLE public.saq_merchant_pci_emails OWNER TO axia;

--
-- Name: saq_merchant_survey_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_merchant_survey_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    saq_merchant_id uuid NOT NULL,
    saq_merchant_id_old character varying(36),
    saq_survey_id uuid NOT NULL,
    saq_survey_id_old character varying(36),
    saq_eligibility_survey_id uuid,
    saq_eligibility_survey_id_old character varying(36) DEFAULT NULL::character varying,
    saq_confirmation_survey_id uuid,
    saq_confirmation_survey_id_old character varying(36) DEFAULT NULL::character varying,
    datestart timestamp without time zone NOT NULL,
    datecomplete timestamp without time zone,
    ip character varying(15) DEFAULT NULL::character varying,
    acknowledgement_name character varying(255) DEFAULT NULL::character varying,
    acknowledgement_title character varying(255) DEFAULT NULL::character varying,
    acknowledgement_company character varying(255) DEFAULT NULL::character varying,
    resolution text
);


ALTER TABLE public.saq_merchant_survey_xrefs OWNER TO axia;

--
-- Name: saq_merchants; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_merchants (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    merchant_name character varying(255) NOT NULL,
    merchant_email character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    email_sent timestamp without time zone,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone
);


ALTER TABLE public.saq_merchants OWNER TO axia;

--
-- Name: saq_prequalifications; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_prequalifications (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    saq_merchant_id uuid NOT NULL,
    saq_merchant_id_old character varying(36),
    result character varying(4) NOT NULL,
    date_completed timestamp without time zone NOT NULL,
    control_scan_code integer,
    control_scan_message text
);


ALTER TABLE public.saq_prequalifications OWNER TO axia;

--
-- Name: saq_questions; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_questions (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    question text NOT NULL
);


ALTER TABLE public.saq_questions OWNER TO axia;

--
-- Name: saq_survey_question_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_survey_question_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    saq_survey_id uuid NOT NULL,
    saq_survey_id_old character varying(36),
    saq_question_id uuid NOT NULL,
    saq_question_id_old character varying(36),
    priority integer NOT NULL
);


ALTER TABLE public.saq_survey_question_xrefs OWNER TO axia;

--
-- Name: saq_surveys; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.saq_surveys (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(255) NOT NULL,
    saq_level character varying(4) DEFAULT NULL::character varying,
    eligibility_survey_id uuid,
    eligibility_survey_id_old character varying(36) DEFAULT NULL::character varying,
    confirmation_survey_id uuid,
    confirmation_survey_id_old character varying(36) DEFAULT NULL::character varying
);


ALTER TABLE public.saq_surveys OWNER TO axia;

--
-- Name: schema_migrations; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.schema_migrations (
    id integer NOT NULL,
    class character varying(255) NOT NULL,
    type character varying(50) NOT NULL,
    created timestamp without time zone NOT NULL
);


ALTER TABLE public.schema_migrations OWNER TO axia;

--
-- Name: schema_migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.schema_migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.schema_migrations_id_seq OWNER TO axia;

--
-- Name: schema_migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.schema_migrations_id_seq OWNED BY public.schema_migrations.id;


--
-- Name: shipping_type_items; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.shipping_type_items (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    shipping_type_id uuid,
    shipping_type_old integer,
    shipping_type_description character varying(50) NOT NULL
);


ALTER TABLE public.shipping_type_items OWNER TO axia;

--
-- Name: shipping_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.shipping_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    shipping_type_old integer,
    shipping_type_description character varying(50) NOT NULL
);


ALTER TABLE public.shipping_types OWNER TO axia;

--
-- Name: sponsor_banks; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.sponsor_banks (
    id uuid NOT NULL,
    bank_name character varying(255) NOT NULL
);


ALTER TABLE public.sponsor_banks OWNER TO axia;

--
-- Name: subregions; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.subregions (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    region_id uuid NOT NULL,
    organization_id uuid NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.subregions OWNER TO axia;

--
-- Name: system_transactions; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.system_transactions (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    system_transaction_id_old character varying(36),
    transaction_type_id uuid NOT NULL,
    transaction_type_old character varying(36),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    merchant_id uuid,
    merchant_id_old character varying(36) DEFAULT NULL::character varying,
    session_id character varying(50) DEFAULT NULL::character varying,
    client_address character varying(100) DEFAULT NULL::character varying,
    system_transaction_date date,
    system_transaction_time time without time zone,
    merchant_note_id uuid,
    merchant_note_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_change_id uuid,
    change_id_old character varying(36) DEFAULT NULL::character varying,
    merchant_ach_id uuid,
    ach_seq_number_old integer,
    order_id uuid,
    order_id_old character varying(36) DEFAULT NULL::character varying,
    programming_id uuid,
    programming_id_old character varying(36) DEFAULT NULL::character varying,
    login_date timestamp without time zone
);


ALTER TABLE public.system_transactions OWNER TO axia;

--
-- Name: tgates; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tgates (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    tg_rate numeric(15,4) DEFAULT NULL::numeric,
    tg_per_item numeric(15,4) DEFAULT NULL::numeric,
    tg_statement numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.tgates OWNER TO axia;

--
-- Name: tickler_availabilities; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_availabilities (
    id character varying(36) NOT NULL,
    name character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.tickler_availabilities OWNER TO axia;

--
-- Name: tickler_availabilities_leads; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_availabilities_leads (
    id character varying(36) NOT NULL,
    availability_id character varying(36) DEFAULT NULL::character varying,
    lead_id character varying(36) DEFAULT NULL::character varying
);


ALTER TABLE public.tickler_availabilities_leads OWNER TO axia;

--
-- Name: tickler_comments; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_comments (
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
-- Name: tickler_companies; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_companies (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_companies OWNER TO axia;

--
-- Name: tickler_equipments; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_equipments (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_equipments OWNER TO axia;

--
-- Name: tickler_followups; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_followups (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_followups OWNER TO axia;

--
-- Name: tickler_leads; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_leads (
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
    website character varying(2008) NOT NULL,
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
-- Name: tickler_leads_logs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_leads_logs (
    id character varying(36) NOT NULL,
    action character varying(10) DEFAULT 'add'::character varying NOT NULL,
    lead_id character varying(36) NOT NULL,
    status_id character varying(36) NOT NULL,
    created timestamp without time zone
);


ALTER TABLE public.tickler_leads_logs OWNER TO axia;

--
-- Name: tickler_loggable_logs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_loggable_logs (
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

CREATE SEQUENCE public.tickler_loggable_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tickler_loggable_logs_id_seq OWNER TO axia;

--
-- Name: tickler_loggable_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.tickler_loggable_logs_id_seq OWNED BY public.tickler_loggable_logs.id;


--
-- Name: tickler_referers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_referers (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_referers OWNER TO axia;

--
-- Name: tickler_resellers; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_resellers (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.tickler_resellers OWNER TO axia;

--
-- Name: tickler_schema_migrations; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_schema_migrations (
    id integer NOT NULL,
    class character varying(255) NOT NULL,
    type character varying(50) NOT NULL,
    created timestamp without time zone NOT NULL
);


ALTER TABLE public.tickler_schema_migrations OWNER TO axia;

--
-- Name: tickler_schema_migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: axia
--

CREATE SEQUENCE public.tickler_schema_migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tickler_schema_migrations_id_seq OWNER TO axia;

--
-- Name: tickler_schema_migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: axia
--

ALTER SEQUENCE public.tickler_schema_migrations_id_seq OWNED BY public.tickler_schema_migrations.id;


--
-- Name: tickler_states; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_states (
    id character varying(36) NOT NULL,
    name character varying(2) NOT NULL,
    state character varying(30) DEFAULT NULL::character varying
);


ALTER TABLE public.tickler_states OWNER TO axia;

--
-- Name: tickler_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.tickler_statuses (
    id character varying(36) NOT NULL,
    name character varying(100) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone,
    "order" integer
);


ALTER TABLE public.tickler_statuses OWNER TO axia;

--
-- Name: timeline_entries; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.timeline_entries (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    timeline_item_id uuid NOT NULL,
    timeline_item_old character varying(50),
    timeline_date_completed date,
    action_flag boolean
);


ALTER TABLE public.timeline_entries OWNER TO axia;

--
-- Name: timeline_items; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.timeline_items (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    timeline_item_old character varying(50),
    timeline_item_description character varying(100) DEFAULT NULL::character varying
);


ALTER TABLE public.timeline_items OWNER TO axia;

--
-- Name: transaction_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.transaction_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    transaction_type_old character varying(36),
    transaction_type_description character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.transaction_types OWNER TO axia;

--
-- Name: usaepay_rep_gtwy_add_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.usaepay_rep_gtwy_add_costs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(255) NOT NULL,
    cost numeric(15,4) NOT NULL
);


ALTER TABLE public.usaepay_rep_gtwy_add_costs OWNER TO axia;

--
-- Name: usaepay_rep_gtwy_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.usaepay_rep_gtwy_costs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(255) NOT NULL,
    cost numeric(15,4) NOT NULL
);


ALTER TABLE public.usaepay_rep_gtwy_costs OWNER TO axia;

--
-- Name: user_bet_tables; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.user_bet_tables (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    bet_id uuid,
    bet_code_old character varying(50),
    user_id uuid NOT NULL,
    user_id_old character varying(36),
    network_id uuid,
    network_old character varying(20),
    rate numeric(15,4) DEFAULT NULL::numeric,
    pi numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.user_bet_tables OWNER TO axia;

--
-- Name: user_compensation_profiles; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.user_compensation_profiles (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    partner_user_id uuid,
    is_partner_rep boolean DEFAULT false,
    is_default boolean DEFAULT true,
    is_profile_option_1 integer DEFAULT 0 NOT NULL,
    is_profile_option_2 integer DEFAULT 0 NOT NULL,
    role_id uuid
);


ALTER TABLE public.user_compensation_profiles OWNER TO axia;

--
-- Name: user_costs_archives; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.user_costs_archives (
    id uuid NOT NULL,
    merchant_pricing_archive_id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    user_id uuid NOT NULL,
    cost_pct numeric,
    per_item_cost numeric,
    statement_cost numeric,
    monthly_statement_cost numeric,
    risk_assmnt_pct numeric,
    risk_assmnt_per_item numeric,
    attrition_reduction_pct numeric,
    ref_p_type character varying(255),
    ref_p_value numeric,
    res_p_type character varying(255),
    res_p_value numeric,
    ref_p_pct numeric,
    res_p_pct numeric,
    res_profit_pct numeric,
    refer_profit_pct numeric,
    is_hidden boolean DEFAULT false
);


ALTER TABLE public.user_costs_archives OWNER TO axia;

--
-- Name: user_parameter_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.user_parameter_types (
    id character varying(36) NOT NULL,
    name character varying(255) NOT NULL,
    "order" integer NOT NULL,
    type integer NOT NULL,
    value_type integer NOT NULL
);


ALTER TABLE public.user_parameter_types OWNER TO axia;

--
-- Name: user_parameters; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.user_parameters (
    id uuid NOT NULL,
    user_parameter_type_id character varying(36) DEFAULT NULL::character varying,
    merchant_acquirer_id uuid,
    products_services_type_id uuid,
    associated_user_id uuid,
    type character varying(255) DEFAULT NULL::character varying,
    value numeric,
    created timestamp without time zone,
    modified timestamp without time zone,
    is_multiple integer DEFAULT 0 NOT NULL,
    user_compensation_profile_id uuid
);


ALTER TABLE public.user_parameters OWNER TO axia;

--
-- Name: user_residual_options; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.user_residual_options (
    id uuid NOT NULL,
    name character varying(64) NOT NULL
);


ALTER TABLE public.user_residual_options OWNER TO axia;

--
-- Name: user_types; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.user_types (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_type_old character varying(36),
    user_type_description character varying(50) DEFAULT NULL::character varying
);


ALTER TABLE public.user_types OWNER TO axia;

--
-- Name: users; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.users (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id_old character varying(36),
    user_type_id uuid,
    user_type_old character varying(36),
    user_title character varying(20) DEFAULT NULL::character varying,
    user_first_name character varying(50) DEFAULT NULL::character varying,
    user_last_name character varying(50) DEFAULT NULL::character varying,
    username character varying(50) DEFAULT NULL::character varying,
    password_2 character varying(50) DEFAULT NULL::character varying,
    user_email character varying(50) DEFAULT NULL::character varying,
    user_phone character varying(20) DEFAULT NULL::character varying,
    user_fax character varying(20) DEFAULT NULL::character varying,
    user_admin character varying(1) DEFAULT NULL::character varying,
    parent_user_id uuid,
    parent_user_id_old character varying(36),
    user_commission integer,
    inactive_date date,
    last_login_date timestamp without time zone,
    last_login_ip character varying(12) DEFAULT NULL::character varying,
    entity_id uuid,
    entity_old character varying(36) DEFAULT NULL::character varying,
    active integer,
    initials character varying(5) DEFAULT NULL::character varying,
    manager_percentage numeric(15,4) DEFAULT NULL::numeric,
    date_started date,
    split_commissions boolean,
    bet_extra_pct boolean,
    secondary_parent_user_id uuid,
    secondary_parent_user_id_old character varying(36) DEFAULT NULL::character varying,
    manager_percentage_secondary numeric(15,4) DEFAULT NULL::numeric,
    discover_bet_extra_pct boolean,
    password character varying(100),
    user_residual_option_id uuid,
    is_blocked boolean DEFAULT false NOT NULL,
    referer_id uuid,
    womply_user_enabled boolean DEFAULT true,
    womply_active boolean DEFAULT true,
    access_token character varying(40),
    api_password character varying(200),
    api_last_request timestamp without time zone,
    bank_name character varying(60),
    routing_number character varying(255),
    account_number character varying(255),
    secret character varying(100),
    opt_out_2fa boolean DEFAULT false,
    wrong_log_in_count integer DEFAULT 0,
    pw_reset_hash character varying(32)
);


ALTER TABLE public.users OWNER TO axia;

--
-- Name: users_products_risks; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.users_products_risks (
    id uuid NOT NULL,
    merchant_id uuid NOT NULL,
    user_id uuid NOT NULL,
    products_services_type_id uuid NOT NULL,
    risk_assmnt_pct numeric,
    risk_assmnt_per_item numeric
);


ALTER TABLE public.users_products_risks OWNER TO axia;

--
-- Name: users_roles; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.users_roles (
    id uuid NOT NULL,
    role_id uuid NOT NULL,
    user_id uuid NOT NULL
);


ALTER TABLE public.users_roles OWNER TO axia;

--
-- Name: uw_approvalinfo_merchant_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_approvalinfo_merchant_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    uw_approvalinfo_id uuid NOT NULL,
    approvalinfo_id_old character varying(36),
    uw_verified_option_id uuid,
    verified_option_id_old character varying(36),
    notes text
);


ALTER TABLE public.uw_approvalinfo_merchant_xrefs OWNER TO axia;

--
-- Name: uw_approvalinfos; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_approvalinfos (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    verified_type public.uw_verified_type NOT NULL
);


ALTER TABLE public.uw_approvalinfos OWNER TO axia;

--
-- Name: uw_infodoc_merchant_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_infodoc_merchant_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    uw_infodoc_id uuid NOT NULL,
    infodoc_id_old character varying(36),
    uw_received_id uuid,
    received_id_old character varying(36),
    notes text
);


ALTER TABLE public.uw_infodoc_merchant_xrefs OWNER TO axia;

--
-- Name: uw_infodocs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_infodocs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    required boolean NOT NULL
);


ALTER TABLE public.uw_infodocs OWNER TO axia;

--
-- Name: uw_receiveds; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_receiveds (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(11) NOT NULL,
    priority integer
);


ALTER TABLE public.uw_receiveds OWNER TO axia;

--
-- Name: uw_status_merchant_xrefs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_status_merchant_xrefs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    uw_status_id uuid NOT NULL,
    status_id_old character varying(36),
    datetime timestamp without time zone NOT NULL,
    notes text
);


ALTER TABLE public.uw_status_merchant_xrefs OWNER TO axia;

--
-- Name: uw_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_statuses (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(99) NOT NULL,
    priority integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.uw_statuses OWNER TO axia;

--
-- Name: uw_verified_options; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.uw_verified_options (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old character varying(36),
    name character varying(37) NOT NULL,
    verified_type public.uw_verified_type NOT NULL
);


ALTER TABLE public.uw_verified_options OWNER TO axia;

--
-- Name: vendors; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.vendors (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    vendor_id_old character varying(36),
    vendor_description character varying(50) NOT NULL,
    rank integer
);


ALTER TABLE public.vendors OWNER TO axia;

--
-- Name: virtual_check_webs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.virtual_check_webs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    vcweb_mid character varying(20) DEFAULT NULL::character varying,
    vcweb_web_based_rate numeric(15,4) DEFAULT NULL::numeric,
    vcweb_web_based_pi numeric(15,4) DEFAULT NULL::numeric,
    vcweb_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    vcweb_gateway_fee numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.virtual_check_webs OWNER TO axia;

--
-- Name: virtual_checks; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.virtual_checks (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    vc_mid character varying(20) DEFAULT NULL::character varying,
    vc_web_based_rate numeric(15,4) DEFAULT NULL::numeric,
    vc_web_based_pi numeric(15,4) DEFAULT NULL::numeric,
    vc_monthly_fee numeric(15,4) DEFAULT NULL::numeric,
    vc_gateway_fee numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.virtual_checks OWNER TO axia;

--
-- Name: visa_bets; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.visa_bets (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    bet_code_old character varying(36),
    bet_processing_rate numeric(15,4) DEFAULT NULL::numeric,
    bet_processing_rate_2 numeric(15,4) DEFAULT NULL::numeric,
    bet_processing_rate_3 numeric(15,4) DEFAULT NULL::numeric,
    bet_per_item_fee numeric(15,4) DEFAULT NULL::numeric,
    bet_per_item_fee_2 numeric(15,4) DEFAULT NULL::numeric,
    bet_per_item_fee_3 numeric(15,4) DEFAULT NULL::numeric,
    bet_business_rate numeric(15,4) DEFAULT NULL::numeric,
    bet_business_rate_2 numeric(15,4) DEFAULT NULL::numeric,
    bet_per_item_fee_bus numeric(15,4) DEFAULT NULL::numeric,
    bet_per_item_fee_bus_2 numeric(15,4) DEFAULT NULL::numeric,
    bet_extra_pct numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.visa_bets OWNER TO axia;

--
-- Name: warranties; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.warranties (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    warranty_old integer,
    warranty_description character varying(50) NOT NULL,
    cost numeric(15,4) NOT NULL
);


ALTER TABLE public.warranties OWNER TO axia;

--
-- Name: web_ach_rep_costs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.web_ach_rep_costs (
    id uuid NOT NULL,
    user_compensation_profile_id uuid NOT NULL,
    rep_rate_pct numeric,
    rep_per_item numeric,
    rep_monthly_cost numeric
);


ALTER TABLE public.web_ach_rep_costs OWNER TO axia;

--
-- Name: webpasses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.webpasses (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    merchant_id uuid NOT NULL,
    merchant_id_old character varying(36),
    wp_rate numeric(15,4) DEFAULT NULL::numeric,
    wp_per_item numeric(15,4) DEFAULT NULL::numeric,
    wp_statement numeric(15,4) DEFAULT NULL::numeric
);


ALTER TABLE public.webpasses OWNER TO axia;

--
-- Name: womply_actions; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.womply_actions (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old integer,
    action character varying(20)
);


ALTER TABLE public.womply_actions OWNER TO axia;

--
-- Name: womply_merchant_logs; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.womply_merchant_logs (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old integer,
    merchant_id uuid,
    merchant_id_old character varying(20),
    womply_action_id uuid,
    womply_action_id_old integer,
    details text,
    created timestamp without time zone
);


ALTER TABLE public.womply_merchant_logs OWNER TO axia;

--
-- Name: womply_statuses; Type: TABLE; Schema: public; Owner: axia
--

CREATE TABLE public.womply_statuses (
    id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    id_old integer,
    status character varying(20)
);


ALTER TABLE public.womply_statuses OWNER TO axia;

--
-- Name: onlineapp_apips id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_apips ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_apips_id_seq'::regclass);


--
-- Name: onlineapp_applications id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_applications ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_applications_id_seq'::regclass);


--
-- Name: onlineapp_cobranded_application_aches id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobranded_application_aches ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_cobranded_application_aches_id_seq'::regclass);


--
-- Name: onlineapp_cobranded_application_values id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobranded_application_values ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_cobranded_application_values_id_seq'::regclass);


--
-- Name: onlineapp_cobranded_applications id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobranded_applications ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_cobranded_applications_id_seq'::regclass);


--
-- Name: onlineapp_cobrands id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobrands ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_cobrands_id_seq'::regclass);


--
-- Name: onlineapp_coversheets id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_coversheets ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_coversheets_id_seq'::regclass);


--
-- Name: onlineapp_email_timeline_subjects id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_email_timeline_subjects ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_email_timeline_subjects_id_seq'::regclass);


--
-- Name: onlineapp_email_timelines id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_email_timelines ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_email_timelines_id_seq'::regclass);


--
-- Name: onlineapp_epayments id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_epayments ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_epayments_id_seq'::regclass);


--
-- Name: onlineapp_groups id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_groups ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_groups_id_seq'::regclass);


--
-- Name: onlineapp_template_fields id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_template_fields ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_template_fields_id_seq'::regclass);


--
-- Name: onlineapp_template_pages id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_template_pages ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_template_pages_id_seq'::regclass);


--
-- Name: onlineapp_template_sections id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_template_sections ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_template_sections_id_seq'::regclass);


--
-- Name: onlineapp_templates id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_templates ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_templates_id_seq'::regclass);


--
-- Name: onlineapp_users id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_users_id_seq'::regclass);


--
-- Name: onlineapp_users_onlineapp_cobrands id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_cobrands ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_users_onlineapp_cobrands_id_seq'::regclass);


--
-- Name: onlineapp_users_onlineapp_templates id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_templates ALTER COLUMN id SET DEFAULT nextval('public.onlineapp_users_onlineapp_templates_id_seq'::regclass);


--
-- Name: schema_migrations id; Type: DEFAULT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.schema_migrations ALTER COLUMN id SET DEFAULT nextval('public.schema_migrations_id_seq'::regclass);


--
-- Data for Name: ach_providers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.ach_providers (id, provider_name) FROM stdin;
5501ec0e-abe4-474e-a5d0-2f2034627ad4	Sage Payments
5501ec0e-6b8c-47c8-b96f-2f2034627ad4	Check Gateway
55bbcdd0-ed3c-4745-918a-1f3634627ad4	Vericheck
\.


--
-- Data for Name: ach_rep_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.ach_rep_costs (id, user_compensation_profile_id, ach_provider_id, rep_rate_pct, rep_per_item, rep_monthly_cost) FROM stdin;
5328acfa-5c18-45c7-85cc-2b1434627ad4	5570e7ec-38a8-4fc2-afc8-337a34627ad4	550d554f-7fa8-4a5a-86c3-0d8c34627ad4	0.11	0.11	11
5328acfa-b65c-4536-9152-2b1434627ad4	5570e7ec-38a8-4fc2-afc8-337a34627ad4	550d554f-8694-4a03-bb75-0d8c34627ad4	0.10	0.10	10
\.


--
-- Data for Name: aches; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.aches (id, merchant_id, merchant_id_old, ach_mid, ach_expected_annual_sales, ach_average_transaction, ach_estimated_max_transaction, ach_written_pre_auth, ach_nonwritten_pre_auth, ach_merchant_initiated_perc, ach_consumer_initiated_perc, ach_monthly_gateway_fee, ach_monthly_minimum_fee, ach_statement_fee, ach_batch_upload_fee, ach_reject_fee, ach_add_bank_orig_ident_fee, ach_file_fee, ach_eft_ccd_nw, ach_eft_ccd_w, ach_eft_ppd_nw, ach_eft_ppd_w, ach_application_fee, ach_expedite_fee, ach_tele_training_fee, ach_mi_w_dsb_bank_name, ach_mi_w_dsb_routing_number, ach_mi_w_dsb_account_number, ach_mi_w_fee_bank_name, ach_mi_w_fee_routing_number, ach_mi_w_fee_account_number, ach_mi_w_rej_bank_name, ach_mi_w_rej_routing_number, ach_mi_w_rej_account_number, ach_mi_nw_dsb_account_number, ach_mi_nw_dsb_routing_number, ach_mi_nw_dsb_bank_name, ach_mi_nw_fee_bank_name, ach_mi_nw_fee_routing_number, ach_mi_nw_fee_account_number, ach_mi_nw_rej_bank_name, ach_mi_nw_rej_routing_number, ach_mi_nw_rej_account_number, ach_ci_w_dsb_bank_name, ach_ci_w_dsb_routing_number, ach_ci_w_dsb_account_number, ach_ci_w_fee_bank_name, ach_ci_w_fee_routing_number, ach_ci_w_fee_account_number, ach_ci_w_rej_bank_name, ach_ci_w_rej_routing_number, ach_ci_w_rej_account_number, ach_ci_nw_dsb_bank_name, ach_ci_nw_dsb_routing_number, ach_ci_nw_dsb_account_number, ach_ci_nw_fee_bank_name, ach_ci_nw_fee_routing_number, ach_ci_nw_fee_account_number, ach_ci_nw_rej_bank_name, ach_ci_nw_rej_routing_number, ach_ci_nw_rej_account_number, ach_rate, ach_per_item_fee, ach_provider_id, ach_risk_assessment) FROM stdin;
551581c7-2510-4606-8106-cfa2a3853998	0954a13a-3072-49a8-b013-c5aeeb167402	3948000031001793	3948000031001793	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2.0000	\N	0.2500	0.1500	0.1500	0.1500	0.1500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
79814156-86e3-4995-aa8b-a87fa9e4bfbd	7c2d713c-654b-4f4b-a6ce-51a3b5a2b383	3948000030002777	3948000030002777	48000.0000	50.0000	2000.0000	\N	\N	\N	\N	\N	\N	\N	\N	2.0000	\N	0.2500	0.1500	0.1500	0.1500	0.1500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
66dda015-f433-47f3-aaf9-e3031807752c	77814d49-4029-42d8-bac5-92d44a22f9d6	3948000030002917	3948000030002917	36000.0000	300.0000	3000.0000	\N	\N	\N	\N	\N	\N	\N	\N	2.0000	\N	0.2500	0.1500	0.1500	0.1500	0.1500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
10f6bb3d-b058-48e4-9ec6-41526edc91a4	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	3948000030003045	3948000030003045	2000.0000	250.0000	1999.9900	\N	\N	\N	\N	\N	\N	\N	\N	2.0000	\N	0.2500	0.1500	0.1500	0.1500	0.1500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
2bd4b3df-c1d1-411e-8e70-581ba01472ba	af28a757-c325-4e4b-a9bb-0e8b1634c629	3948906204000019	3948906204000019	250000.0000	100.0000	1000.0000	90.0000	10.0000	5.0000	95.0000	\N	\N	\N	\N	2.0000	\N	0.2500	0.1500	0.1500	0.1500	0.1500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
3187bdf4-5dcd-43d6-b3e2-8cdbe2ca0430	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	3948000030002785	3948000030002785	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	5.0000	\N	2.5000	1.0000	1.0000	1.0000	1.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0.5000	0.25	\N	\N
95b9fb2f-f7b5-4e25-b620-d2fa4c48a979	cef25db7-3c95-470d-8015-af138ddf408e	3948000030001962	3948000030001962	12000.0000	100.0000	500.0000	100.0000	\N	100.0000	\N	12.5000	\N	\N	\N	1.0000	\N	0.2500	0.0850	0.0850	0.0850	0.0850	9.9500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
4ad8446f-810b-4da4-83c3-821d10163fa7	756b5550-912f-4d1c-b8a1-a21a80fc38ad	7641110042403579	7641110042403579	\N	\N	\N	\N	\N	\N	\N	\N	\N	5.0000	\N	2.0000	\N	0.5000	0.5000	0.5000	0.5000	0.5000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0.5000	\N	\N	\N
c7ece5f1-e0b5-4b23-986e-8711d4151619	65873e09-f556-41e6-9751-1811ae5e361d	7641110042406622	7641110042406622	13000000.0000	1500.0000	3000.0000	\N	\N	\N	\N	0.0000	\N	0.0000	\N	2.0000	\N	\N	0.1000	0.1000	0.1000	0.1000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0.2500	\N	\N	\N
55cc6dd8-1d68-4c4c-b3cb-18aa34627ad4	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	Lorem ipsum dolor sit amet	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	tdGYA+LVUNxE4dHOjlpt6F83I3ECRun65tjxPjVte4CdotlEUERq0SwJ81aOOwLZzBwEA7nYfYbLgaqz/v4t/w==	0.0000	0	\N	0
\.


--
-- Data for Name: addl_amex_rep_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.addl_amex_rep_costs (id, user_compensation_profile_id, conversion_fee, sys_processing_fee) FROM stdin;
55b9f412-0ac0-4a85-9931-139734627ad4	5570e7fe-5a64-4a93-8724-337a34627ad4	0.1	0.11
557f085f-94ac-4b70-a98c-35fd34627ad4	5570e7d2-8f80-4374-bbf4-337a34627ad4	0.3	0.15
557f085f-b29c-4f47-9087-35fd34627ad4	5570e7d2-172c-4e84-b1c0-337a34627ad4	0.3	0.15
557f085f-c218-4257-a522-35fd34627ad4	5570e7d2-4ff0-47ac-ae85-337a34627ad4	0.3	0.15
557f085f-ced8-4734-8687-35fd34627ad4	5570e7d2-6014-48f3-93da-337a34627ad4	0.3	0.15
\.


--
-- Data for Name: address_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.address_types (id, address_type_old, address_type_description) FROM stdin;
795f442e-04ab-43d1-a7f8-f9ba685b90ac	BUS	Business Address
1c7c0709-86df-4643-8f7f-78bd28259008	CORP	Corporate Address
31e277ff-423a-4af8-9042-8310d7c320df	OWN	Owner Address
63494506-9aed-4d7c-b83c-898e6254ff20	BANK	Bank Address
edf125da-8592-42e3-bd26-ff0614bd27ba	MAIL	Mail Address
\.


--
-- Data for Name: addresses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.addresses (id, address_id_old, merchant_id, merchant_id_old, address_type_id, address_type_old, merchant_owner_id, merchant_owner_id_old, address_title, address_street, address_city, address_state, address_zip, address_phone, address_fax, address_phone2, address_phone_ext, address_phone2_ext) FROM stdin;
00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000003	Lorem ipsum dolor sit amet	795f442e-04ab-43d1-a7f8-f9ba685b90ac	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	business-address	street-1	city-1	1	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
00000000-0000-0000-0000-000000000002	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000003	Lorem ipsum dolor sit amet	63494506-9aed-4d7c-b83c-898e6254ff20	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000002	Lorem ipsum dolor sit amet	bank-address	street-2	city-2	2	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
00000000-0000-0000-0000-000000000003	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000003	Lorem ipsum dolor sit amet	edf125da-8592-42e3-bd26-ff0614bd27ba	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000003	Lorem ipsum dolor sit amet	bank-address	street-3	city-3	3	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
00000000-0000-0000-0000-000000000004	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000003	Lorem ipsum dolor sit amet	1c7c0709-86df-4643-8f7f-78bd28259008	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000004	Lorem ipsum dolor sit amet	bank-address	street-4	city-4	4	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
00000000-0000-0000-0000-000000000005	Lorem ipsum dolor sit amet	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	Lorem ipsum dolor sit amet	795f442e-04ab-43d1-a7f8-f9ba685b90ac	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000005	Lorem ipsum dolor sit amet	business-address	street-1	city-1	1	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
00000000-0000-0000-0000-000000000006	Lorem ipsum dolor sit amet	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	Lorem ipsum dolor sit amet	63494506-9aed-4d7c-b83c-898e6254ff20	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000006	Lorem ipsum dolor sit amet	bank-address	street-2	city-2	2	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
00000000-0000-0000-0000-000000000007	Lorem ipsum dolor sit amet	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	Lorem ipsum dolor sit amet	edf125da-8592-42e3-bd26-ff0614bd27ba	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000007	Lorem ipsum dolor sit amet	bank-address	street-3	city-3	3	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
00000000-0000-0000-0000-000000000008	Lorem ipsum dolor sit amet	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	Lorem ipsum dolor sit amet	1c7c0709-86df-4643-8f7f-78bd28259008	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000008	Lorem ipsum dolor sit amet	bank-address	street-4	city-4	4	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	Lor
\.


--
-- Data for Name: adjustments; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.adjustments (id, adj_seq_number_old, user_id, user_id_old, adj_date, adj_description, adj_amount) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	2015-03-01	Adjustment 1	1.0000
00000000-0000-0000-0000-000000000002	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	2012-08-15	Adjustment 2	0.5000
\.


--
-- Data for Name: admin_entity_views; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.admin_entity_views (id, user_id, user_id_old, entity_id, entity_old) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N
\.


--
-- Data for Name: amexes; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.amexes (id, merchant_id, merchant_id_old, amex_processing_rate, amex_per_item_fee) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	1.0000	1.0000
\.


--
-- Data for Name: api_configurations; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.api_configurations (id, configuration_name, auth_type, instance_url, authorization_url, access_token_url, redirect_url, client_id, client_secret, access_token, access_token_lifetime_hours, refresh_token, issued_at) FROM stdin;
\.


--
-- Data for Name: app_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.app_statuses (id, merchant_ach_app_status_id, rep_cost, axia_cost, rep_expedite_cost, axia_expedite_cost_tsys, axia_expedite_cost_sage, user_compensation_profile_id) FROM stdin;
53763c81-7d58-4341-bc12-385a34627ad4	ec4a7409-0ad7-48be-9d83-08356f3b16fe	20	0	50	0	0	6570e7dc-a4fc-444c-8929-337a34627ad4
64863c81-7d58-4341-bc12-385a34627ad4	fd5a7409-0ad7-48be-9d83-08356f3b16fe	30	0	50	0	0	6570e7dc-a4fc-444c-8929-337a34627ad4
\.


--
-- Data for Name: articles; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.articles (id, title, body) FROM stdin;
\.


--
-- Data for Name: associated_external_records; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.associated_external_records (id, external_system_name, merchant_id) FROM stdin;
\.


--
-- Data for Name: associated_users; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.associated_users (id, user_id, associated_user_id, role, permission_level, user_compensation_profile_id, main_association) FROM stdin;
b3b8ca8e-a69c-4396-ad96-e53b3c2362e0	00ccf87a-4564-4b95-96e5-e90df32c46c1	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	Sales Manager	SM	5570e7fe-5a64-4a93-8724-337a34627ad4	f
3d889475-6a19-40ef-93c8-f1a9b4adaf12	00ccf87a-4564-4b95-96e5-e90df32c46c1	113114ae-7777-7777-7777-fa0c0f25c786	Installer	Installer	5570e7fe-5a64-4a93-8724-337a34627ad4	f
47d089ad-e6eb-4b7a-a449-238d30984566	113114ae-8888-8888-8888-fa0c0f25c786	00ccf87a-4564-4b95-96e5-e90df32c46c1	Rep	Rep	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	f
00000000-0000-0000-0000-000000000001	00ccf87a-4564-4b95-96e5-e90df32c46c1	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	Sales Manager	SM	5570e7fe-5a64-4a93-8724-337a34627ad4	f
00000000-0000-0000-0000-000000000002	00ccf87a-4564-4b95-96e5-e90df32c46c1	00000000-0000-0000-0000-000000000999	Installer	Installer	43265df7-6b97-4f86-9e6f-8638eb30cd9e	f
00000000-0000-0000-0000-000000000003	00ccf87a-4564-4b95-96e5-e90df32c46c1	113114ae-7777-7777-7777-fa0c0f25c786	Installer	Installer	5570e7fe-5a64-4a93-8724-337a34627ad4	f
00000000-0000-0000-0000-000000000004	113114ae-8888-8888-8888-fa0c0f25c786	00ccf87a-4564-4b95-96e5-e90df32c46c1	Rep	Rep	00000000-0000-0000-0000-999999999999	f
\.


--
-- Data for Name: attrition_ratios; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.attrition_ratios (id, associated_user_id, percentage, user_compensation_profile_id) FROM stdin;
561d9fcf-d1ac-4cba-87d6-3b25c0a81cd6	02eb04ab-1f70-4f80-bc83-e16f9a86e764	10	\N
561d9fcf-0558-477a-864c-3b25c0a81cd6	ffd135d7-1305-4723-9705-7790635d53c1	10	\N
564a6d86-b9b0-466f-b704-0755c0a81cd6	02eb04ab-1f70-4f80-bc83-e16f9a86e764	0	\N
564a71c3-697c-4490-8965-1e55c0a81cd6	02eb04ab-1f70-4f80-bc83-e16f9a86e764	0	\N
564a73a5-5450-4aa2-993f-1cedc0a81cd6	02eb04ab-1f70-4f80-bc83-e16f9a86e764	0	\N
\.


--
-- Data for Name: authorizes; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.authorizes (id, merchant_id, merchant_id_old, mid, transaction_fee, monthly_fee) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	1.0000	1.0000
\.


--
-- Data for Name: back_end_networks; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.back_end_networks (id, network_description) FROM stdin;
55cc6d06-8a18-48ef-83f9-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-9b48-49b5-bc4d-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-a37c-4f5f-ae61-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-aae8-425d-97d7-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-b254-474d-8e76-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-b9c0-42f9-89ee-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-c0c8-4122-aaf2-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-c7d0-4e20-aa24-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-cf3c-44f4-8d2a-176534627ad4	Lorem ipsum dolor sit amet
55cc6d06-d6a8-4289-895f-176534627ad4	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: background_jobs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.background_jobs (id, job_status_id, description, modified) FROM stdin;
\.


--
-- Data for Name: bankcards; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.bankcards (id, merchant_id, merchant_id_old, bc_mid, bet_id, bet_code_old, bc_processing_rate, bc_per_item_fee, bc_monthly_volume, bc_average_ticket, bc_max_transaction_amount, bc_card_present_swipe, bc_card_not_present, bc_card_present_imprint, bc_direct_to_consumer, bc_business_to_business, bc_government, bc_annual_fee, bc_statement_fee, bc_min_month_process_fee, bc_chargeback_fee, bc_aru_fee, bc_voice_auth_fee, bc_te_amex_number, bc_te_amex_auth_fee, bc_te_diners_club_number, bc_te_diners_club_auth_fee, bc_te_discover_number, bc_te_discover_auth_fee, bc_te_jcb_number, bc_te_jcb_auth_fee, bc_pt_monthly_support_fee, bc_pt_online_mer_report_fee, bc_pt_mobile_access_fee, bc_pt_mobile_transaction_fee, bc_pt_application_fee, bc_pt_expedite_fee, bc_pt_mobile_setup_fee, bc_pt_equip_fee, bc_pt_phys_prod_tele_train_fee, bc_pt_equip_reprog_fee, bc_vt_monthly_support_fee, bc_vt_gateway_access_fee, bc_vt_application_fee, bc_vt_expedite_fee, bc_vt_prod_tele_train_fee, bc_vt_lease_rental_deposit, bc_hidden_per_item_fee, bc_te_amex_number_disp, bc_te_diners_club_number_disp, bc_te_discover_number_disp, bc_te_jcb_number_disp, bc_hidden_per_item_fee_cross, bc_eft_secure_flag, bc_pos_partner_flag, bc_pt_online_rep_report_fee, bc_micros_ip_flag, bc_micros_dialup_flag, bc_micros_per_item_fee, bc_te_num_items, bc_wireless_flag, bc_wireless_terminals, bc_usaepay_flag, bc_epay_retail_flag, usaepay_rep_gtwy_cost_id, bc_usaepay_rep_gtwy_cost_id_old, usaepay_rep_gtwy_add_cost_id, bc_usaepay_rep_gtwy_add_cost_id_old, bc_card_not_present_internet, bc_tgate_flag, bc_petro_flag, bc_risk_assessment, bc_gw_gateway_id_old, gateway_id, bc_gw_rep_rate, bc_gw_rep_per_item, bc_gw_rep_statement, bc_gw_rep_features) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	\N	\N	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	Lorem ipsum dolor sit amet	1.0000	Lorem ipsum dolor sit amet	1.0000	Lorem ipsum dolor sit amet	1.0000	Lorem ipsum dolor sit amet	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	Lo	Lo	Lo	Lo	1.0000	1	1	1.0000	1	1	1.0000	1.0000	1	1	1	1	\N	\N	\N	\N	1.0000	1	1	1.0000	\N	\N	1.0000	1.0000	1.0000	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
\.


--
-- Data for Name: bet_networks; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.bet_networks (id, name, is_active) FROM stdin;
55cc6d99-d534-45f1-acc7-184d34627ad4	Bet Network 1	1
55cc6d99-e2e0-4119-8f62-184d34627ad4	Bet Network 2	2
55cc6d99-eb14-4783-8e13-184d34627ad4	Lorem ipsum dolor sit amet	3
55cc6d99-f2e4-43da-90bc-184d34627ad4	Lorem ipsum dolor sit amet	4
55cc6d99-fab4-453c-8190-184d34627ad4	Lorem ipsum dolor sit amet	5
55cc6d99-0220-47c7-a89a-184d34627ad4	Lorem ipsum dolor sit amet	6
55cc6d99-09f0-465c-99b2-184d34627ad4	Lorem ipsum dolor sit amet	7
55cc6d99-115c-406d-9120-184d34627ad4	Lorem ipsum dolor sit amet	8
55cc6d99-18c8-4979-8399-184d34627ad4	Lorem ipsum dolor sit amet	9
55cc6d99-2098-41a3-80f8-184d34627ad4	Lorem ipsum dolor sit amet	10
56e05859-7d90-4777-9fde-31a834627ad4	TSYS (pre 10/1/14)	1
\.


--
-- Data for Name: bet_tables; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.bet_tables (id, name, bet_extra_pct, card_type_id, is_enabled) FROM stdin;
558ade3b-f5ec-43e1-bd89-20ba34627ad4	3145	0	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-ffcc-4b94-b86d-20ba34627ad4	3143	0	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-9990-4f05-bfed-20ba34627ad4	3005	0.05	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-2f08-4407-b1cd-20ba34627ad4	3010	0.1	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-b4e0-49db-be56-20ba34627ad4	3015	0.15	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-3a54-4c9c-bd8e-20ba34627ad4	3020	0.2	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-cf04-494c-b890-20ba34627ad4	3025	0.25	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-58c4-4614-b389-20ba34627ad4	3030	0.3	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-dfc8-4ba2-aaef-20ba34627ad4	3035	0.35	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-65a0-4ec4-980e-20ba34627ad4	3040	0.4	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-4618-4bb7-b815-20ba34627ad4	3045	0.45	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-00ac-4d8e-8347-20ba34627ad4	3050	0.5	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-8b34-4647-b336-20ba34627ad4	3055	0.55	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-1300-4313-ba74-20ba34627ad4	3060	0.6	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-aa08-4065-ab35-20ba34627ad4	3065	0.65	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-2fe0-45e5-81fc-20ba34627ad4	3070	0.7	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-cdf0-40c5-b6fd-20ba34627ad4	3075	0.75	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
558ade3b-51d4-46d1-bf25-20ba34627ad4	5140	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-dbf8-4e65-bc56-20ba34627ad4	5141	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-5618-4931-bd7c-20ba34627ad4	5142	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-d4e8-430c-8d43-20ba34627ad4	5143	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-56d8-40e4-856f-20ba34627ad4	5144	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-e354-46e8-9c49-20ba34627ad4	5145	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-6738-4d10-8039-20ba34627ad4	5146	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-e1bc-4f3b-91d7-20ba34627ad4	5605	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-6988-4a32-8a0e-20ba34627ad4	5610	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-f474-4d92-865a-20ba34627ad4	5611	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-6f5c-49f3-8426-20ba34627ad4	5182	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-ec9c-4f34-8fb6-20ba34627ad4	5171	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-6720-4b07-b6dd-20ba34627ad4	5172	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-7140-47c0-be88-20ba34627ad4	5174	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-ec8c-45d4-9425-20ba34627ad4	5175	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-6648-4126-b761-20ba34627ad4	6601	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-f38c-4d4a-8b4a-20ba34627ad4	5612	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-6e10-4dda-8b55-20ba34627ad4	5221	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-e95c-4db7-b506-20ba34627ad4	5201	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-64a8-4860-978d-20ba34627ad4	5202	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-26a8-44f0-a296-20ba34627ad4	5203	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-a258-48f7-a9ae-20ba34627ad4	5143sp	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
5555555b-4444-3333-960d-20ba34627ad4	5000	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-1da4-43a1-960d-20ba34627ad4	6000	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
5eb6c8c8-bfa0-476c-9545-9ca9a20d83da	6144	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-1200-48b8-afb3-20ba34627ad4	6147	10	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-8ce8-4726-9e3e-20ba34627ad4	6602	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-0a28-4c31-82a1-20ba34627ad4	5005	0.05	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-a25c-4d3f-a725-20ba34627ad4	5010	0.1	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-29c4-4b17-98d2-20ba34627ad4	5015	0.15	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-b514-404e-9547-20ba34627ad4	5020	0.2	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-4bb8-45c3-86c7-20ba34627ad4	5025	0.25	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-d640-4bbd-83a7-20ba34627ad4	5030	0.3	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-676c-48a9-83db-20ba34627ad4	5035	0.35	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-f0c8-44c1-ad75-20ba34627ad4	5040	0.4	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-876c-4974-9492-20ba34627ad4	5045	0.45	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-1064-455f-9ab2-20ba34627ad4	5050	0.5	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-d4bc-4d00-b24f-20ba34627ad4	5055	0.55	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-65e8-4878-88ba-20ba34627ad4	5060	0.6	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-2144-4fd2-8219-20ba34627ad4	5065	0.65	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-a910-44b3-b591-20ba34627ad4	5070	0.7	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-30dc-4ef4-b961-20ba34627ad4	5075	0.75	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
558ade3b-b588-4f64-9bfe-20ba34627ad4	7140	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-49d4-4519-b36b-20ba34627ad4	7141	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-ca34-4455-8933-20ba34627ad4	7142	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-4648-4c9e-8b1f-20ba34627ad4	7143	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-c2c0-43e7-917a-20ba34627ad4	7144	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-5e78-4368-a2b7-20ba34627ad4	7145	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-daf0-46a9-a406-20ba34627ad4	7146	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-9584-47ad-80e6-20ba34627ad4	7605	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-11fc-460e-8329-20ba34627ad4	7610	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-9ce8-48ca-912f-20ba34627ad4	7611	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-17d0-4a46-9e27-20ba34627ad4	7182	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-9830-4aac-bcb7-20ba34627ad4	7171	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-137c-4abf-9d93-20ba34627ad4	7172	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-a37c-4db0-9532-20ba34627ad4	7173	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-1ec8-48fc-9b9b-20ba34627ad4	7174	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-ba80-4832-bd6c-20ba34627ad4	7175	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-3630-4be6-bc9b-20ba34627ad4	8601	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3b-c248-46e1-ab41-20ba34627ad4	7612	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-5d5c-4499-9b2f-20ba34627ad4	7221	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-d7e0-4db3-876e-20ba34627ad4	7201	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-5200-4705-acb7-20ba34627ad4	7202	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-e138-4419-b28e-20ba34627ad4	7203	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-5b58-48d1-81aa-20ba34627ad4	7143sp	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-d6a4-416f-8645-20ba34627ad4	8000	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-518c-4fb7-8c9e-20ba34627ad4	8144	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-d69c-4b9b-9795-20ba34627ad4	8147	10	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-53dc-4b10-a208-20ba34627ad4	8602	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-d180-423c-a50d-20ba34627ad4	7005	0.05	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-61e4-40db-af12-20ba34627ad4	7010	0.1	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-f7c0-4a1c-b963-20ba34627ad4	7015	0.15	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-80b8-4a95-bb99-20ba34627ad4	7020	0.2	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-0dfc-4708-8e12-20ba34627ad4	7025	0.25	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-949c-4f8a-a9ac-20ba34627ad4	7030	0.3	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-2820-467f-a31c-20ba34627ad4	7035	0.35	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-c6f8-42d4-8141-20ba34627ad4	7040	0.4	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-4d98-465a-9fcb-20ba34627ad4	7045	0.45	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-d62c-4b46-9567-20ba34627ad4	7050	0.5	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-6adc-499c-8e15-20ba34627ad4	7055	0.55	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-f244-4fe1-8183-20ba34627ad4	7060	0.6	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-78e4-4fdb-88e9-20ba34627ad4	7065	0.65	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-ff84-464e-acfe-20ba34627ad4	7070	0.7	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-a8e8-4e96-9282-20ba34627ad4	7075	0.75	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
558ade3c-2d94-45ea-8ba1-20ba34627ad4	9350	0	5581dfa8-8004-4b13-8448-093534627ad4	t
558ade3c-ac64-4022-b1db-20ba34627ad4	9360	0	5581dfa8-8004-4b13-8448-093534627ad4	t
558ade3c-3b38-492f-adb4-20ba34627ad4	1000	0	5581dfa8-7788-4db5-b1c5-093534627ad4	t
89239d4c-15a1-4252-a25f-cb030439f456	6140	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
f993e7bd-3c36-40a9-b396-f39d857084a9	6141	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
eeda321b-f4a9-46ec-a94e-5ef9181c421b	6142	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
2825934e-ca71-4785-8824-9d9c761b1e55	6343	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
558ade3c-fc90-4ed7-9ab6-20ba34627ad4	6144	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
961bf7ac-1f3a-44d8-8014-8c315d3a7745	6345	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
9e637ec6-3eab-4664-b007-9b113ddc1256	6146	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
09ca424a-0b09-47f6-b545-f34be6c5f2fc	6160	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
5a40d600-7e74-4f04-97d9-de0778ac59ae	6161	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
558ade3c-6604-41b5-9fc3-20ba34627ad4	6162	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
73a79b8a-90a3-4b34-9a4f-7ff2ae3ddf14	6775	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
3643896c-cd98-4513-835e-4b70dd0e364f	6201	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
5ef12990-5eff-4ed1-9823-d2de2cbde946	6202	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
558ade3c-9fd8-46f9-b191-20ba34627ad4	6203	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
9fd4bf62-95fa-45ad-8e12-630eae185251	6343sp	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
484f88f8-c3ac-48db-a967-af612c1772fd	6005	0.05	d82f4282-f816-4880-975c-53d42a7f02bc	t
88b9fcd6-c313-4993-aff7-e1a3d34f05e6	6010	0.1	d82f4282-f816-4880-975c-53d42a7f02bc	t
81ce3bcb-9dc0-4898-b82e-641c31c600f0	6015	0.15	d82f4282-f816-4880-975c-53d42a7f02bc	t
120cff80-6628-4d32-acbc-9979afd10735	6020	0.2	d82f4282-f816-4880-975c-53d42a7f02bc	t
d6a1886c-0f2f-42d9-b512-0c4952c12b10	6025	0.25	d82f4282-f816-4880-975c-53d42a7f02bc	t
86e37ff3-0a38-4d2a-a3e7-1a19d28e3b16	6030	0.3	d82f4282-f816-4880-975c-53d42a7f02bc	t
8a33675a-57cd-4a9b-ac8e-ef2637657c16	6035	0.35	d82f4282-f816-4880-975c-53d42a7f02bc	t
f916fb56-9e4a-423b-bea7-d6790f9e21e6	6040	0.4	d82f4282-f816-4880-975c-53d42a7f02bc	t
20092f3f-fe85-4b64-ae01-32858a2eb152	6045	0.45	d82f4282-f816-4880-975c-53d42a7f02bc	t
6bdf3bf7-b625-4f11-acf0-82606726737d	6050	0.5	d82f4282-f816-4880-975c-53d42a7f02bc	t
79740240-95a6-446d-a144-e932410c7b96	6055	0.55	d82f4282-f816-4880-975c-53d42a7f02bc	t
03f6626f-c6da-45b6-acc7-8352e7eab729	6060	0.6	d82f4282-f816-4880-975c-53d42a7f02bc	t
4facd6d9-d6e0-40f3-afc6-e790eb775cb7	6065	0.65	d82f4282-f816-4880-975c-53d42a7f02bc	t
74e97110-7f28-4469-8bf4-ac171df2a1f8	6070	0.7	d82f4282-f816-4880-975c-53d42a7f02bc	t
fb37224a-5d68-499c-abc2-a1c892e0fde7	6075	0.75	d82f4282-f816-4880-975c-53d42a7f02bc	t
7a29b5cc-9740-47b1-87fd-900f5f711684	6143CPtsys	0	\N	f
603f3da5-d389-4e31-90db-6430fbc53f9c	6150	0	\N	f
4729ff9a-b9d0-419e-aa17-14eb22eb7814	6742N	0	\N	f
86c31837-a90b-411e-95eb-2b0f1ce5f603	6743N	0	\N	f
fcae5f9c-3a28-44c3-8d68-7f32813488dc	6744N	0	\N	f
dfb5ff7d-57f0-45cf-9245-492452ce82e0	6745N	0	\N	f
8e998a22-9295-4108-a72e-824486e0c9ea	6746N	0	\N	f
ae8522ce-3b97-42dc-b23c-e7f6db0ed3e2	6747N	0	\N	f
86fa598d-8908-4b79-beaf-86fd7066355f	6753N	0	\N	f
5070d966-17ff-4d11-bec9-231afc8f3e4d	6080	0	\N	f
13bf671a-b69f-4dd3-9656-ace96e4d51f4	6090	0	\N	f
afd2de09-92ce-4540-b56f-d4b514690b05	6200	0	\N	f
371b4694-b091-41ab-b9b1-86dc34f0eadc	6343sp04	0	\N	f
b6204bf4-5566-4284-a189-9e2581946f90	6343Dial	0	\N	f
24f053bc-100f-436a-aa89-aa4361650eed	6345Dial	0	\N	f
ec8c058d-ad34-4cac-a945-710f3a78793f	6343NonDial	0	\N	f
e6efbe5a-be9c-40d8-9762-cbfdc8ed27de	6345NonDial	0	\N	f
4a3af44a-855e-4ff3-b0c2-72678d275eda	6065Dial	0	\N	f
c71754c8-e1c9-40ef-a561-c5ba0ef26ac1	6065NonDial	0	\N	f
4b2a1670-d2d5-414e-be91-09c22a8ef8c7	6070Dial	0	\N	f
19faf01a-c3bc-4734-852b-3e8a80787af9	6070NonDial	0	\N	f
7538201f-ae4c-4662-b90f-bfe6fc150105	6075Dial	0	\N	f
bf57bba0-22dc-437a-b5cb-a2f86a57bafb	6075NonDial	0	\N	f
410ac479-c74e-428b-93fd-f054007c1f00	6005Dial	0	\N	f
2ccc856a-7d09-4fb7-888a-aa38706a2b16	6010Dial	0	\N	f
3914686a-fdf9-4c39-b093-f5918f6a2565	6015Dial	0	\N	f
bff766c1-e50e-4eee-a711-e86a21c3d8c8	6020Dial	0	\N	f
339d2001-9e1a-4fc5-acc5-debd71d42c43	6025Dial	0	\N	f
a664acad-6bf5-4a60-b388-9f3660937a1d	6030Dial	0	\N	f
e8353357-4cea-4eb8-87f4-6390249e1518	6035Dial	0	\N	f
7e8d3063-ab23-482f-8985-bdb67d8b142e	6040Dial	0	\N	f
215a87b5-4358-4b72-a7bf-8ad6694097e2	6045Dial	0	\N	f
de642c94-a154-42eb-856a-c6c4a1a5f316	6050Dial	0	\N	f
24cf1845-0d56-4778-8e4c-085de50acfb4	6055Dial	0	\N	f
30089fdd-8ce0-496e-a2c4-68e48614eb84	6060Dial	0	\N	f
71dc3ada-51ff-4ba0-9096-424fbfafd33c	6005NonDial	0	\N	f
f9771643-7597-4fed-ba1d-d2e2c48f3b03	6010NonDial	0	\N	f
361bc96c-4f40-4cd6-9ea8-cc4e187b89ed	6015NonDial	0	\N	f
0c1f547f-1a88-4602-be94-a5e854b4e54b	6020NonDial	0	\N	f
235cad48-b501-465d-8ac3-80e58e96e290	6025NonDial	0	\N	f
743bf9d3-2107-4a51-a58a-b46642e758bc	6030NonDial	0	\N	f
74541bfd-c8c5-462b-80ef-efcd91c268a1	6035NonDial	0	\N	f
611d4097-8448-4f0a-9ca7-17abae42a398	6040NonDial	0	\N	f
32f7cf60-7c9a-42b5-ae86-1a5c4ae55743	6045NonDial	0	\N	f
d9c6d0cc-1e98-4879-9ae3-540666944e10	6050NonDial	0	\N	f
d9cd3b27-44c3-46c8-ba5a-49d3d851ff80	6055NonDial	0	\N	f
aa717a8b-23db-40cf-92d1-77e337334dbf	6060NonDial	0	\N	f
5db39e2d-0ff5-4440-ae0d-59a70c740f83	6140Dial	0	\N	f
2f7f896c-1dad-4a89-8a54-b408df95771d	6140NonDial	0	\N	f
fc94c735-5ca5-4f9d-84c4-ec6afd5ceba2	6141Dial	0	\N	f
6f7fd17b-dc0a-4be6-9bf6-889beffe5f0d	6141NonDial	0	\N	f
9b5614bc-567e-4705-b1f0-df2c00d23adc	6142Dial	0	\N	f
979df4ec-e600-4042-9e81-79c3e6ab1b78	6142NonDial	0	\N	f
09a4ea33-609f-427a-9a6a-fe2f886f9728	6144Dial	0	\N	f
73afaef2-f35a-42d5-89c1-ca08efcc330f	6144NonDial	0	\N	f
9c092115-7ddb-465f-b121-bd25bb34bf35	6146Dial	0	\N	f
5f0e7314-710b-43a7-99f5-6b28ed912aef	6146NonDial	0	\N	f
56df682f-9514-4fb5-b656-078234627ad4	6801	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-0880-44a6-aaba-078234627ad4	7080	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-5a64-41bb-8bdd-078234627ad4	7085	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-b6d4-4115-9a14-078234627ad4	7090	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-14d4-4bda-90a9-078234627ad4	7102	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-b004-469a-a4f1-078234627ad4	7178	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-02b0-4452-968a-078234627ad4	7180	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-64fc-42f6-bb99-078234627ad4	7181	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-b230-40e1-9735-078234627ad4	7183	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-0ea0-4922-93c5-078234627ad4	7184	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-6b74-4444-ac4a-078234627ad4	8603	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
56df682f-c460-48cb-b2a4-078234627ad4	5080	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-b6ac-4dbc-b63a-078234627ad4	5085	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-0f98-4f51-be03-078234627ad4	5090	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-5b3c-4f00-a55e-078234627ad4	5102	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-5d68-416e-a1b2-078234627ad4	5173	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-05e0-4703-b8df-078234627ad4	5178	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-68f4-47d7-a34a-078234627ad4	5180	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-b81c-42b7-b2a9-078234627ad4	5181	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-067c-4414-9377-078234627ad4	5183	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-57fc-458f-a25c-078234627ad4	5184	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-d21c-45f9-9b03-078234627ad4	8603	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
56df682f-2400-41bd-974f-078234627ad4	6080	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
56df682f-74b8-4908-b298-078234627ad4	6085	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
56df682f-cc78-421d-8df3-078234627ad4	6090	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
56df682f-217c-477a-8278-078234627ad4	6145	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
56df682f-6718-42d9-add8-078234627ad4	6605	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
56df682f-d2c4-4aea-8ac5-078234627ad4	6610	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
56df682f-781c-43aa-b267-078234627ad4	3201	0	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
56df682f-c6e0-4e67-b12e-078234627ad4	3202	0	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
56df682f-1158-4ac5-9b60-078234627ad4	3203	0	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
56df682f-7a48-4887-8410-078234627ad4	8888	0	5581dfa8-7788-4db5-b1c5-093534627ad4	t
395c0ddc-a35c-4a3a-8aee-cfc1273cc6b1	6221	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
521df34a-0a90-4035-b90e-461534627ad4	5612/7612tsys	0.0000	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
521df34a-bf80-4ff2-8628-478c34627ad4	5035/7035	0.3500	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
521df34a-66bc-4de3-83aa-4ec634627ad4	5050/7050tsys	0.5000	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
521df34a-d678-4b4d-ab58-485934627ad4	6146/8146Res	\N	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
521df34a-4168-43b9-8516-434134627ad4	5171/7171tsys	0.0000	d3216cb3-ee71-40c6-bede-ac9818e24f3a	f
521df34a-8ef8-4e52-aa44-410934627ad4	5141/7141	0.0000	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
521df34a-8e04-43f2-be91-4fb634627ad4	5174/7174tsys	0.0000	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
521df34a-94e0-4aa1-b6ac-4c6634627ad4	5140/7140	0.0000	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
521df34b-3d98-464e-a542-479f34627ad4	5020/7020tsys	0.2000	\N	t
521df34b-2344-4624-ab8e-4d9a34627ad4	6144/8144Adj	0.1500	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
521df34b-16bc-4f68-add2-4ce734627ad4	5143/7143tsysR	0.0000	\N	f
521df34b-13ac-4793-a56b-495e34627ad4	5171/7171	0.0000	\N	t
521df34b-5abc-45cc-b741-426a34627ad4	5035/7035tsys	0.3500	\N	t
521df34b-1798-49d2-85dc-452934627ad4	6148/8148tsysN	0.2000	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
521df34b-1790-48a3-9768-47fc34627ad4	6603/8603	0.0000	\N	t
521df34b-5850-4d3b-aef1-489b34627ad4	5025/7025tsys	0.2500	\N	t
521df34b-64d4-4adc-b262-455e34627ad4	6146/8146tsysR	0.0000	ddea093f-ab15-44f6-87ba-fb4c4235ced1	t
521df34b-2728-441e-9f87-41d834627ad4	5177/7177	0.0000	\N	f
521df34b-d2a8-465a-a899-451b34627ad4	6147/8147Res	\N	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
5739a131-056c-4582-bca3-17c934627ad4	6221	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
f52ecb82-dc5e-4122-898d-256f5da32f46	4000	0	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	t
d901273e-38f5-47ec-9bf3-1a684d0c3310	4100	0	d3216cb3-ee71-40c6-bede-ac9818e24f3a	t
1cea6c3a-6a80-403d-a7ac-cf0c39818915	4200	0	d82f4282-f816-4880-975c-53d42a7f02bc	t
\.


--
-- Data for Name: bets; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.bets (id, bet_network_id, bet_table_id, card_type_id, pct_cost, pi_cost, additional_pct, sales_cost, auth_cost, dial_auth_cost, non_dial_auth_cost, non_dial_auto_cost, settlement_cost, user_compensation_profile_id, non_dial_sales_cost, dial_sales_cost) FROM stdin;
00000000-0000-0000-0000-000000000001	55cc6d99-d534-45f1-acc7-184d34627ad4	521df34a-0a90-4035-b90e-461534627ad4	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	\N	\N	\N	\N	\N	\N	\N	\N	\N	5570e7fe-5a64-4a93-8724-337a34627ad4	0	100
00000000-0000-0000-0000-000000000002	55cc6d99-0220-47c7-a89a-184d34627ad4	521df34a-d678-4b4d-ab58-485934627ad4	ddea093f-ab15-44f6-87ba-fb4c4235ced1	\N	\N	\N	\N	\N	\N	\N	\N	\N	5570e7dc-a4fc-444c-8929-337a34627ad4	0	75
\.


--
-- Data for Name: brands; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.brands (id, name) FROM stdin;
02daf34e-6789-49a2-b750-b1bb06e046d5	Axia Med
d38411ea-82d0-4f69-ae48-c728f6b96a03	Axia Tech
16dd937f-8878-4d99-ab74-6eab1ea05bde	Axia Payments
\.


--
-- Data for Name: cancellation_fees; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.cancellation_fees (id, cancellation_fee_id_old, cancellation_fee_description) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: card_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.card_types (id, card_type_old, card_type_description) FROM stdin;
e9580d94-29ce-4de4-9fd4-c81d1afefbf4	\N	Visa
d3216cb3-ee71-40c6-bede-ac9818e24f3a	\N	Mastercard
5581dfa8-8004-4b13-8448-093534627ad4	\N	Debit
5581dfa8-7788-4db5-b1c5-093534627ad4	\N	EBT
ddea093f-ab15-44f6-87ba-fb4c4235ced1	\N	American Express
d82f4282-f816-4880-975c-53d42a7f02bc	\N	Discover
d0ba718a-9296-4cdd-a6be-1d6d52e9c66c	\N	Diner's Club
95a6b6af-c084-4ad9-bfc1-f406f0df1601	\N	JCB
\.


--
-- Data for Name: change_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.change_types (id, change_type_old, change_type_description) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: check_guarantee_providers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.check_guarantee_providers (id, provider_name) FROM stdin;
52e6bad2-d1a4-4c1d-b86a-2b8034627ad4	Certegy
52e6bad2-cf04-492d-9dc3-2b8034627ad4	CrossCheck
52e6bad2-3984-4d0a-ba29-2b8034627ad4	Global eTelecom
\.


--
-- Data for Name: check_guarantee_service_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.check_guarantee_service_types (id, service_type) FROM stdin;
52e6bad2-c324-4f51-b368-2b8034627ad4	Check Guarantee
52e6bad2-adc4-4b10-a26d-2b8034627ad4	Check Verification
52e6bad2-fa94-4d04-b467-2b8034627ad4	Electronic Check Conversion (No Guarantee)
52e6bad2-56a0-4b7a-9b2e-2b8034627ad4	Electronic Check Conversion (With Guarantee)
52e6bad2-b694-4ff4-ae2f-2b8034627ad4	Remote Deposit/Check 21
\.


--
-- Data for Name: check_guarantees; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.check_guarantees (id, merchant_id, merchant_id_old, cg_mid, cg_station_number, cg_account_number, cg_transaction_rate, cg_per_item_fee, cg_monthly_fee, cg_monthly_minimum_fee, cg_application_fee, check_guarantee_provider_id, check_guarantee_service_type_id, rep_processing_rate_pct, rep_per_item_cost, rep_monthly_cost, cg_risk_assessment) FROM stdin;
dd7f7689-a017-458b-984f-7528179a79da	3da018f6-414a-4b5d-b6f5-e6dd8e21820b	\N	3948000031001462	1058293100	28739600000048	0.1000	0.1000	10.0000	15.0000	5.0000	52e6bad2-cf04-492d-9dc3-2b8034627ad4	52e6bad2-adc4-4b10-a26d-2b8034627ad4	0.1	0.1	10	0.1
aa8b3263-1daa-4941-b947-3739817a8712	2f6585e1-9518-40c9-b581-f00949238027	\N	3948000030001135	1056549600	27941400000043	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
879e6757-81aa-4104-a8ff-a398e3698980	dbea1465-0cca-4f8f-ac37-1b3c237d1bbe	\N	4541619030000125	1057679708	21968401000644	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
11fc4a4d-ede2-4a52-ba00-f297f2b263ab	9d81436c-2e13-4b66-a0da-bd7f7cabbbf2	\N	4541619030000269	1052365801	21968401000644	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
febbed8c-6186-413c-950f-fdd0facbe36a	5cf5c633-307d-4bd3-b033-5123662ac3a9	\N	4541619740000774	1052608409	27170901000241	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.clients (id, client_id_global, client_name_global) FROM stdin;
\.


--
-- Data for Name: commission_fees; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.commission_fees (id, associated_user_id, is_do_not_display, app_fee_profit, app_fee_loss, non_app_fee_profit, non_app_fee_loss, created, modified, user_compensation_profile_id) FROM stdin;
534bd780-1718-400a-bb96-2ad934627ad4	\N	0	50	50	100	0	2014-04-14 14:41:36	2014-04-14 14:41:36	6570e7dc-a4fc-444c-8929-337a34627ad4
634bd780-1718-400a-bb96-2ad934627ad4	\N	0	100	100	100	100	2014-04-14 14:41:36	2014-04-14 14:41:36	5570e7dc-a4fc-444c-8929-337a34627ad4
734bd780-1718-400a-bb96-2ad934627ad4	\N	0	50	50	50	50	2014-04-14 14:41:36	2014-04-14 14:41:36	7570e7dc-a4fc-444c-8929-337a34627ad4
834bd780-1718-400a-bb96-2ad934627ad4	\N	0	50	50	50	50	2014-04-14 14:41:36	2014-04-14 14:41:36	8570e7dc-a4fc-444c-8929-337a34627ad4
\.


--
-- Data for Name: commission_pricings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.commission_pricings (id, merchant_id, merchant_id_old, user_id, user_id_old, c_month, c_year, multiple, r_rate_pct, r_per_item_fee, r_statement_fee, m_rate_pct, m_per_item_fee, m_statement_fee, m_avg_ticket, m_monthly_volume, bet_table_id, bet_code_old, referer_id, ref_seq_number_old, bet_extra_pct, reseller_id, res_seq_number_old, products_services_type_id, products_services_type_id_old, num_items, ref_p_value, res_p_value, r_risk_assessment, ref_p_type, res_p_type, ref_p_pct, res_p_pct, partner_rate_pct, partner_per_item, partner_statement_fee, rep_product_profit_pct, manager_id, secondary_manager_id, partner_id, referrer_id, product_group_id, rep_gross_profit, rep_pct_of_gross, partner_rep_rate, partner_rep_per_item_fee, partner_rep_statement_fee, partner_rep_gross_profit, partner_rep_pct_of_gross, manager_rate, manager_per_item_fee, manager_statement_fee, manager_gross_profit, manager_pct_of_gross, manager2_rate, manager2_per_item_fee, manager2_statement_fee, manager2_gross_profit, manager2_pct_of_gross, partner_rate, partner_per_item_fee, partner_gross_profit, partner_pct_of_gross, referrer_rate, referrer_per_item_fee, referrer_statement_fee, referrer_gross_profit, referrer_pct_of_gross, reseller_rate, reseller_per_item_fee, reseller_statement_fee, reseller_gross_profit, reseller_pct_of_gross, merchant_state, multiple_amount, r_profit_pct, r_profit_amount, partner_rep_profit_pct, partner_rep_profit_amount, manager_profit_pct, manager_profit_amount, manager2_profit_pct, manager2_profit_amount, partner_profit_pct, partner_profit_amount, referrer_profit_pct, referrer_profit_amount, reseller_profit_pct, reseller_profit_amount, gross_profit, original_m_rate_pct, manager_multiple, manager_multiple_amount, manager2_multiple, manager2_multiple_amount) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000004	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	5	2015	1.0000	0.0000	0.0400	5.0000	0.0300	0.2300	5.0000	0.0000	50.0000	5739a131-056c-4582-bca3-17c934627ad4	\N	\N	\N	\N	\N	\N	72a445f3-3937-4078-8631-1f569d6a30ed	DBT	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	\N	5	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	1.5	\N	2
00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000004	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	11	2015	5.0000	0.0000	0.0000	5.0000	0.0000	0.2000	0.0000	0.0000	6000.0000	5739a131-056c-4582-bca3-17c934627ad4	\N	\N	\N	\N	\N	\N	a293737b-34c5-4caf-82dd-40d41bb9e0e1	DBT	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	\N	15.67	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2.5	\N	3
00000000-0000-0000-0000-000000000003	00000000-0000-0000-0000-000000000003	\N	32165df7-6b97-4f86-9e6f-8638eb30cd9e	\N	1	2016	2.5000	0.0000	0.0400	5.0000	0.0300	0.1500	5.0000	0.0000	25.0000	5739a131-056c-4582-bca3-17c934627ad4	\N	\N	\N	\N	\N	\N	72a445f3-3937-4078-8631-1f569d6a30ed	DISC	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	100	2	0	0	0	50	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	0	55.5	0	\N	25	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	3.5	\N	4
\.


--
-- Data for Name: commission_reports; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.commission_reports (id, c_month, c_year, c_retail, c_rep_cost, c_shipping, c_app_rtl, c_app_cost, c_install, c_expecting, c_from_nw1, c_other_cost, c_from_other, c_business, user_id, user_id_old, status, order_id, order_id_old, axia_invoice_number, merchant_ach_id, ach_seq_number_old, description, merchant_id, merchant_id_old, split_commissions, referer_id, ref_seq_number_old, reseller_id, res_seq_number_old, partner_id, partner_id_old, partner_exclude_volume, partner_cost, shipping_type_id, partner_rep_profit, partner_profit, tax_amount) FROM stdin;
00000000-0000-0000-0000-000000000001	7	2015	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	A	00000000-0000-0000-0000-000000000001	\N	1	\N	\N	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000001	\N	t	\N	\N	\N	\N	\N	\N	t	0.11	00000000-0000-0000-0000-000000000001	1	1	1.50
00000000-0000-0000-0000-000000000002	11	2015	2.0000	1.0000	1.0000	1.0000	1.0000	1.0000	2.0000	1.0000	1.0000	1.0000	1.0000	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	I	00000000-0000-0000-0000-000000000001	\N	1	\N	\N	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000004	\N	t	\N	\N	\N	\N	\N	\N	t	0.11	00000000-0000-0000-0000-000000000001	1	1	1.60
00000000-0000-0000-0000-000000000003	2	2015	3.0000	1.0000	1.0000	1.0000	1.0000	1.0000	3.0000	1.0000	1.0000	1.0000	1.0000	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	A	00000000-0000-0000-0000-000000000001	\N	1	\N	\N	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000004	\N	t	\N	\N	\N	\N	\N	\N	t	0.11	00000000-0000-0000-0000-000000000001	1	1	1.70
\.


--
-- Data for Name: control_scan_merchant_sync_results; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.control_scan_merchant_sync_results (id, new_merchant_count, updated_merchant_count, unboarded_merchant_count, created) FROM stdin;
\.


--
-- Data for Name: debit_acquirers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.debit_acquirers (id, debit_acquirer_id_old, debit_acquirers) FROM stdin;
7b897a42-5474-4dff-9c2d-d4c31db37270	\N	Existing Merchants TSYS
7b545cdb-2a79-4d06-892d-f37d7c06e6aa	\N	Existing Merchants Sage
10d433c2-250c-46ab-b394-ad1cf680e6f9	\N	New Merchants TSYS
2cdabf1c-d304-4805-aeaf-733a01c056ac	\N	Direct Connect
5aecdefd-8e8a-4d5d-aca8-d47980857498	\N	Sage PTI
\.


--
-- Data for Name: debits; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.debits (id, merchant_id, merchant_id_old, mid, transaction_fee, monthly_fee, monthly_volume, monthly_num_items, rate_pct, debit_acquirer_id, debit_acquirer_id_old) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	1.0000	1.0000	1.0000	1.0000	1.0000	\N	\N
\.


--
-- Data for Name: discover_bets; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.discover_bets (id, bet_id, bet_code_old, bet_extra_pct) FROM stdin;
3da018f6-414a-4b5d-b6f5-e6dd8e21820b	\N	\N	0.1000
\.


--
-- Data for Name: discover_user_bet_tables; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.discover_user_bet_tables (id, bet_id, bet_code_old, user_id, user_id_old, network, rate, pi) FROM stdin;
d4380dec-5613-4a87-b1bf-ae44e651e13c	9fb78992-a6ee-417b-aa87-21d6c15a4540	\N	58b96fab-15a2-4c16-b5bf-ddec9c7dcf2d	\N		0.0100	0.0100
\.


--
-- Data for Name: discovers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.discovers (id, merchant_id, merchant_id_old, bet_id, bet_code_old, disc_processing_rate, disc_per_item_fee, disc_statement_fee, disc_monthly_volume, disc_average_ticket, disc_risk_assessment, gateway_id, gateway_id_old, disc_gw_rep_rate, disc_gw_rep_per_item, disc_gw_rep_features) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	\N	\N	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	\N	\N	1.0000	1.0000	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
\.


--
-- Data for Name: ebts; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.ebts (id, merchant_id, merchant_id_old, mid, transaction_fee, monthly_fee) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	1.0000	1.0000
\.


--
-- Data for Name: entities; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.entities (id, entity_old, entity_name) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Entity 1
\.


--
-- Data for Name: equipment_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.equipment_costs (id, equipment_item_id, rep_cost, user_compensation_profile_id) FROM stdin;
5509dd2d-4730-4a42-942d-0e200a0100c1	00000000-0000-0000-0000-000000000001	0.0000	00000000-0000-0000-0000-000000000001
5509dd2d-4df0-4e7d-9a61-0e200a0100c1	00000000-0000-0000-0000-000000000002	10.0000	00000000-0000-0000-0000-000000000001
5509dd2d-1248-467d-a463-0e200a0100c1	00000000-0000-0000-0000-000000000003	25.0000	00000000-0000-0000-0000-000000000001
5509dd2d-b120-4b2f-977a-0e200a0100c1	0ad6626a-f67f-4b69-9a13-f12996f9895f	99.0000	\N
5509dd2d-5764-4805-ae2f-0e200a0100c1	5f7acb49-9718-40ad-add3-2e3f32fabafa	99.0000	\N
5509dd2d-0258-4cca-9b4d-0e200a0100c1	5af5fa7f-acb5-4616-ba17-7dcbdaef7b62	285.0000	\N
5509dd2d-9a28-47ca-a7e7-0e200a0100c1	4d9c09b2-f72f-41c3-93b5-51a0f8ed102b	69.0000	\N
5509dd2d-3d4c-406e-9497-0e200a0100c1	f06cb265-5f90-46ff-8469-397f4d0e1d96	99.0000	\N
5509dd2d-e840-4f3c-9a89-0e200a0100c1	c8ae2eb5-b2ea-49f8-b8f0-9e903963e4f3	225.0000	\N
5509dd2d-8d58-4f96-b527-0e200a0100c1	87b9e57b-d18c-47b2-bdbc-4e90018a6fd5	159.0000	\N
5637c65c-8b90-4443-8c88-7da934627ad4	ad2cb963-dafe-4f02-8aaf-ebdde4003e42	10.0000	5637c65c-d934-4744-bce3-7da934627ad4
5637c65c-a14c-402f-bc11-7da934627ad4	c51883ec-aa21-4ec2-91b1-704e214571fc	25.0000	5637c65c-d934-4744-bce3-7da934627ad4
5637c65c-9c14-4e7b-8ff6-7da934627ad4	f94ce061-aea7-4428-b32a-06f6666da8ed	99.0000	5637c65c-d934-4744-bce3-7da934627ad4
5637c65c-96dc-4fd0-a142-7da934627ad4	6c5e6da1-47bd-4f06-ace1-04be3a83f445	99.0000	5637c65c-d934-4744-bce3-7da934627ad4
5637c65c-ec54-44e3-8cdc-7da934627ad4	b680bd1c-ff6b-40e5-ac1b-bfd57ffaab44	0.0000	5637c65c-d934-4744-bce3-7da934627ad4
6737c65c-96dc-4fd0-a142-7da934627ad4	7d5e6da1-47bd-4f06-ace1-04be3a83f445	250.0000	8570e7dc-a4fc-444c-8929-337a34627ad4
bea1a3ff-23bc-4e3c-a3c3-7b6500e1f67f	7d5e6da1-47bd-4f06-ace1-04be3a83f445	250.0000	7570e7dc-a4fc-444c-8929-337a34627ad4
\.


--
-- Data for Name: equipment_items; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.equipment_items (id, equipment_item_old, equipment_type_id, equipment_type_old, equipment_item_description, equipment_item_true_price, equipment_item_rep_price, active, warranty) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	Equipment item 1	10.0000	5.0000	1	1
00000000-0000-0000-0000-000000000002	\N	00000000-0000-0000-0000-000000000001	\N	Equipment item 2	20.0000	10.0000	1	0
00000000-0000-0000-0000-000000000003	\N	00000000-0000-0000-0000-000000000002	\N	Equipment item 3	30.0000	15.0000	1	1
00000000-0000-0000-0000-000000000004	\N	00000000-0000-0000-0000-000000000003	\N	Equipment item 4	40.0000	20.0000	0	1
\.


--
-- Data for Name: equipment_programming_type_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.equipment_programming_type_xrefs (id, programming_id_old, programming_type, equipment_programming_id) FROM stdin;
53c2cb71-23d8-4628-93fb-4ed9fb281ef5	\N	AVS	00000000-0000-0000-0000-000000000001
5e1929b7-80ee-47f6-b39a-75ee5bd34f82	\N	SD	00000000-0000-0000-0000-000000000001
348c7881-2afd-477b-831f-8b27a390e89e	\N	TIP	00000000-0000-0000-0000-000000000002
b4e83c18-d728-4d88-af25-2b8ecab3238b	\N	SRV	00000000-0000-0000-0000-000000000002
c5213c5a-c1bb-44ba-985e-780a267494aa	\N	NPB	00000000-0000-0000-0000-000000000003
\.


--
-- Data for Name: equipment_programmings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.equipment_programmings (id, equipment_programming_id, programming_id_old, merchant_id, merchant_id_old, terminal_number, hardware_serial, terminal_type, network, provider, app_type, status, date_entered, date_changed, user_id, user_id_old, serial_number, pin_pad, printer, auto_close, chain, agent, gateway_id, gateway_id_old, version) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	\N	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	2012-08-15	2012-08-15	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lore	Lore	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor
00000000-0000-0000-0000-000000000002	\N	\N	00000000-0000-0000-0000-000000000003	\N	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	2012-08-15	2012-08-15	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lore	Lore	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor
00000000-0000-0000-0000-000000000003	\N	\N	00000000-0000-0000-0000-000000000003	\N	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lor	2012-08-15	2012-08-15	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lore	Lore	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor
\.


--
-- Data for Name: equipment_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.equipment_types (id, equipment_type_old, equipment_type_description) FROM stdin;
00000000-0000-0000-0000-000000000001	HW	Equipment type HW
00000000-0000-0000-0000-000000000002	\N	Equipment type 2
00000000-0000-0000-0000-000000000003	\N	Equipment type 3
\.


--
-- Data for Name: external_record_fields; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.external_record_fields (id, merchant_id, associated_external_record_id, field_name, api_field_name, value) FROM stdin;
\.


--
-- Data for Name: gateway0s; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.gateway0s (id, merchant_id, merchant_id_old, gw_rate, gw_per_item, gw_statement, gw_epay_retail_num, gw_usaepay_rep_gtwy_cost_id, gw_usaepay_rep_gtwy_cost_id_old, gw_usaepay_rep_gtwy_add_cost_id, gw_usaepay_rep_gtwy_add_cost_id_old) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	1.0000	1.0000	1.0000	1	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N
\.


--
-- Data for Name: gateway1s; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.gateway1s (id, merchant_id, merchant_id_old, gw1_rate, gw1_per_item, gw1_statement, gw1_rep_rate, gw1_rep_per_item, gw1_rep_statement, gw1_rep_features, gateway_id, gateway_id_old, gw1_monthly_volume, gw1_monthly_num_items, gw1_mid, has_m_link_pi, addl_rep_statement_cost) FROM stdin;
f54b7522-d091-4da3-bfce-86f02410d1f8	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	0.0100	0.0500	12.5000	0.0100	0.0400	40.0000	Silver Package. 12 lic	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
d92b1664-71d6-45b3-bdd3-3f823bb06b46	71b8b8f8-e32d-4066-b399-3649ec758445	\N	0.0000	0.0500	15.0000	0.0000	0.0400	15.0000	Silver package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
f2f10b02-5106-49c3-bb48-569159cfe4db	f39af019-ccb2-41d7-aad7-ddafc95a52f4	\N	0.0000	0.0500	15.0000	0.0000	0.0400	15.0000	Silver Package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
3d3d6464-1638-483f-9a5b-2e5cb2e39b40	99dedd10-04d4-4080-9197-8f3f716f7779	\N	0.0000	0.0500	12.5000	0.0000	0.0400	12.5000	Silver package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
623e5e25-3923-421e-b30b-33befc90ff4e	bc01b1c8-890c-4f73-b9aa-f6255b56bc35	\N	0.0000	0.0800	10.0000	0.0000	0.0400	10.0000	Silver Package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
a1436fa6-4fb2-47a4-b99d-179a2e971194	6ea25e06-6f84-4631-b2af-e9750e20e762	\N	0.0000	0.0500	10.0000	0.0000	0.0400	15.0000	Gold package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
e7032242-4d42-4344-9d00-adda51a6a9b9	e16d65a7-1f5e-4205-82c9-7b49aa1eee94	\N	0.0000	0.0500	10.0000	0.0000	0.0400	10.0000	Silver package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
70701c82-cab2-4e06-973a-ef6a2183d265	b415a341-75fa-47ab-a715-c06f4e9f6af0	\N	0.0000	0.0800	12.5000	0.0000	0.0400	10.0000	Silver package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
aa58f395-6c06-4ce6-8ad4-2f11ed8105f1	c1c0dcdb-918a-4cc2-b7e7-fd5df25a0ab8	\N	0.0000	0.0500	15.0000	0.0000	0.0400	10.0000	Silver package	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
03fb357c-2f98-4912-9a65-061cae94c0e4	4c5d8f41-5a7f-43d4-863c-412b6c321e6d	\N	0.0000	0.0500	12.5000	0.0000	0.0400	12.5000	Silver package, 1 lic	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	\N	\N	\N	f	5
\.


--
-- Data for Name: gateway2s; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.gateway2s (id, merchant_id, merchant_id_old, gw2_rate, gw2_per_item, gw2_statement, gw2_rep_rate, gw2_rep_per_item, gw2_rep_statement, gw2_rep_features, gateway_id, gateway_id_old, gw2_monthly_volume, gw2_monthly_num_items) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.	2e07bda7-9f0e-4785-ab71-9b07ded6f386	\N	1.0000	1.0000
\.


--
-- Data for Name: gateway_cost_structures; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.gateway_cost_structures (id, user_compensation_profile_id, gateway_id, rep_monthly_cost, rep_rate_pct, rep_per_item) FROM stdin;
\.


--
-- Data for Name: gateways; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.gateways (id, id_old, name, enabled, "position") FROM stdin;
3616c30d-3167-4231-98e9-f486432e2899	1	USA ePay	t	1
3db3c7ab-2b66-4fee-b89c-018197c85ce9	2	Sage Virtual Terminal	t	2
9898ae02-3a93-484f-b41b-9261cdfe1db4	3	MerchantLink	t	3
da053da3-a406-4c0b-8726-95a477420c90	4	TGate	t	4
31f426ff-d63a-4070-bc82-a3f558cf1961	5	WebPASS	t	5
3b2f923d-e296-4f6f-bc42-3f4f5c01ac2a	6	CounterPASS	t	6
06be6bfd-2a79-4e23-9fca-270e6b2fcbf3	7	ShopKeep	t	7
33b95e84-3755-437a-9554-1531601e6e77	8	Simply Swipe It	t	8
a536e089-4d6c-4401-ad5a-edb316aca2ca	9	Authorize.net	t	9
5c7a73ef-8092-4e81-bf93-9f70a44df664	0	No Gateway	t	10
567b2192-9ff0-4c80-8417-77c234627ad4	\N	Axia ePay Gold	t	11
567b2192-b654-40dc-a3bb-77c234627ad4	\N	Axia ePay Platinum	t	12
567b2192-aa54-4a1e-838b-77c234627ad4	\N	Axia ePay Silver	t	13
567b2192-44e0-40bf-ba17-77c234627ad4	\N	Blackline	t	14
567b2192-34d4-4d20-a493-77c234627ad4	\N	Bridgepay	t	15
567b2192-d21c-4eca-ae04-77c234627ad4	\N	Cayan	t	16
567b2192-7158-42e9-a5dc-77c234627ad4	\N	Element	t	17
567b2192-7a0c-4869-bd6e-77c234627ad4	\N	MerchantLink Dial	t	18
567b2192-c9fc-4202-a06e-77c234627ad4	\N	MerchantLink IP/SSL	t	19
\.


--
-- Data for Name: gift_card_providers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.gift_card_providers (id, provider_name) FROM stdin;
\.


--
-- Data for Name: gift_cards; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.gift_cards (id, merchant_id, merchant_id_old, gc_mid, gc_loyalty_item_fee, gc_gift_item_fee, gc_chip_card_one_rate_monthly, gc_chip_card_gift_item_fee, gc_chip_card_loyalty_item_fee, gc_smart_card_printing, gc_card_reorder_fee, gc_loyalty_mgmt_database, gc_statement_fee, gc_application_fee, gc_equipment_fee, gc_misc_supplies, gc_merch_prov_art_setup_fee, gc_news_provider_artwork_fee, gc_training_fee, gc_plan, gift_card_provider_id) FROM stdin;
82e41da1-5275-4f4e-a9d0-1a99cd766cb0	3da018f6-414a-4b5d-b6f5-e6dd8e21820b	\N	3948000031001140	0.10	0.4	10	0.1000	0.1000	0.1000	0.10	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	one_rate	52e3268e-5884-461d-b068-486834627ad4
dcb3d512-8809-4f6e-917c-a566a968d858	3da018f6-414a-4b5d-b6f5-e6dd8e21820b	\N	3948000031000947	0.10	0.10	35	0.1000	0.1000	0.1000	0.10	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	one_rate	52e3268e-4a08-4b87-b6c2-486834627ad4
dcb3d512-8809-4f6e-917c-a566a9688858	5321e352-d408-45fb-8bcc-111114627ad4	\N	3948000031000947	0.10	0.10	35	0.1000	0.1000	0.1000	0.10	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	per_item	52e3268e-4a08-4b87-b6c2-486834627ad4
dcb3d512-8809-4f6e-917c-a566a9699958	5321e352-d408-45fb-8bcc-111114627ad4	\N	3948000031000947	0.10	0.10	35	0.1000	0.1000	0.1000	0.10	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	per_item	52e3268e-5884-461d-b068-486834627ad4
\.


--
-- Data for Name: groups; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.groups (id, group_id_old, group_description, active) FROM stdin;
55cc6d31-f814-49ba-bbbf-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-0624-4753-a730-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-0f20-4572-ae76-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-1754-425e-a64b-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-1f88-4bdd-bddc-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-2820-4ace-8559-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-3054-46d6-88be-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-3888-4548-a624-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-40bc-43a6-b097-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
55cc6d31-49b8-4af2-8624-17a334627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
\.


--
-- Data for Name: imported_data_collections; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.imported_data_collections (id, merchant_id, month, year, gw_n1_id, gw_n1_id_seq, gw_n1_item_count, gw_n1_vol, gw_n2_id, gw_n2_id_seq, gw_n2_item_count, gw_n2_vol, pf_total_gw_item_count, pf_total_gw_vol, devices_billed_count, pf_recurring_rev, pf_recurring_item_rev, pf_recurring_device_lic_rev, pf_recurring_gw_rev, pf_recurring_acct_rev, pf_one_time_rev, pf_per_item_fee, pf_item_fee_total, pf_monthly_fees, pf_one_time_cost, pf_rev_share, pf_transactions, pf_actual_mo_vol, acquiring_one_time_rev, acquiring_one_time_cost, ach_recurring_rev, ach_recurring_gp, is_usa_epay, is_pf_gw, mo_gateway_cost, profit_loss_amount, net_sales_count, net_sales, gross_sales_count, gross_sales, discount, interchng_income, interchng_expense, other_income, other_expense, total_income, total_expense, gross_profit, revised_gp, total_income_minus_pf, card_brand_expenses, processor_expenses, sponsor_cogs, ithree_monthly_cogs, sf_closed_date, sf_opportunity_won, sf_projected_accounts, sf_projected_devices, sf_projected_acq_tans, sf_projected_acq_vol, sf_projected_pf_trans, sf_projected_pf_revenue, sf_projected_pf_recurring_ach_revenue, sf_projected_pf_recurring_ach_gp, sf_support_cases_count) FROM stdin;
\.


--
-- Data for Name: invoice_items; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.invoice_items (id, merchant_ach_id, merchant_ach_reason_id, reason_other, commissionable, taxable, non_taxable_reason_id, amount, tax_amount) FROM stdin;
5cdca868-b9e0-4ab3-9314-4c0434627ad4	c19d038b-b2e1-4b73-8362-67d9f5a6c40b	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	50.0000	0.0000
5cdca868-5340-4694-bfdf-4c0434627ad4	6765ac8f-5844-457f-91d0-b0910a46c592	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	195.0000	0.0000
5cdca868-e8b8-42dc-96c1-4c0434627ad4	25928313-8754-4716-8b53-3280e3b1a3f4	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	50.0000	0.0000
5cdca868-6b0c-4e0e-8a3c-4c0434627ad4	e900ec8a-4741-4bc1-ba4b-dab816255843	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	50.0000	0.0000
5cdca868-ec34-4a75-896e-4c0434627ad4	e845dd54-70b2-4b27-a61d-6c5c95af800b	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	125.0000	0.0000
5cdca868-6654-415b-8152-4c0434627ad4	3a96e01c-3c80-46fb-849e-18cd1f3afa41	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	195.0000	0.0000
5cdca868-dc28-4978-a3a2-4c0434627ad4	8a341b34-50ab-420a-8f09-fe44f9967dbc	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	65.0000	0.0000
5cdca868-5a30-4f7b-bb95-4c0434627ad4	56a6ba9a-2098-460f-97b3-ddbab6a6b17b	55104ce5-d7a8-40df-9575-225e34627ad4	\N	f	f	\N	595.0000	\N
5cdca868-e1fc-4de0-9bbf-4c0434627ad4	db3a7409-0ad7-48be-9d83-08356f3b16fe	55104ce5-e688-468e-ae95-225e34627ad4	Other reason	f	f	\N	195.0000	0.0000
5cdca868-58fc-4ec1-ad15-4c0434627ad4	a7b23a26-4c91-4f3c-95d9-741b54166e58	55104ce5-e688-468e-ae95-225e34627ad4	\N	f	f	\N	142.5000	0.0000
5cdca868-0600-4f4b-9809-4c0434627ad4	66a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	t	f	\N	500.0000	\N
5cdca868-70c0-472b-957c-4c0434627ad4	76a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	t	f	\N	95.0000	\N
5cdca868-5584-43e9-bb3e-4c0434627ad4	86a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	t	f	\N	35.0000	\N
5cdca868-16bc-4490-8ba9-4c0434627ad4	96a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	t	f	\N	500.0000	\N
\.


--
-- Data for Name: job_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.job_statuses (id, name, rank) FROM stdin;
709a73b7-be02-44ad-a95f-e009e1601a7e	In Progress	1
b2913141-d790-4dc9-bf64-991d08dbf200	Waiting	2
17d206d2-97f8-4d99-bd1e-c13c349e284f	Error	3
02b4534a-fe71-4db3-ab07-330b1839ecf0	Done	4
\.


--
-- Data for Name: last_deposit_reports; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.last_deposit_reports (id, merchant_id, merchant_id_old, merchant_dba, last_deposit_date, user_id, user_id_old, monthly_volume, sales_num) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000004	\N	16 Hands	2012-08-15	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	1.0000	\N
00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000003	\N	Another Merchant	2013-08-02	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	0.5000	\N
00000000-0000-0000-0000-000000000003	00000000-0000-0000-0000-000000000004	\N	16 Hands	2014-04-09	113114ae-7777-7777-7777-fa0c0f25c786	\N	1.0000	\N
\.


--
-- Data for Name: loggable_logs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.loggable_logs (id, action, model, foreign_key, source_id, content, created, controller_action) FROM stdin;
565d85d0-93ec-4977-bd98-03fbc0a8ad65	newChangeRequest	Merchant	dca93ecb-8309-494a-a555-1aa3cce27077	8ef7f0d5-0c53-46fa-ac09-864868cbfdb7	a:5:{s:8:"complete";s:16:"Save and Approve";s:8:"Merchant";a:35:{s:12:"merchant_mid";s:16:"7641110042407299";s:2:"id";s:36:"dca93ecb-8309-494a-a555-1aa3cce27077";s:12:"merchant_dba";s:16:"Seeking Wifi LLC";s:14:"merchant_email";s:28:"reece.hirano@seekingwifi.com";s:12:"merchant_url";s:17:"Http://apwall.com";s:16:"merchant_contact";s:12:"Reece Hirano";s:25:"merchant_contact_position";s:7:"Manager";s:14:"reporting_user";s:0:"";s:7:"user_id";s:36:"a6029617-64a2-463d-be08-5fdcbfb5b0cd";s:10:"sm_user_id";s:0:"";s:11:"sm2_user_id";s:0:"";s:8:"CardType";a:1:{i:0;s:36:"1451b675-0ccc-44f5-8659-ba90d882d439";}s:10:"network_id";s:0:"";s:19:"back_end_network_id";s:0:"";s:20:"merchant_acquirer_id";s:0:"";s:15:"merchant_bin_id";s:0:"";s:20:"original_acquirer_id";s:0:"";s:14:"bet_network_id";s:36:"55cc6d99-d534-45f1-acc7-184d34627ad4";s:19:"cancellation_fee_id";s:0:"";s:10:"partner_id";s:0:"";s:22:"partner_exclude_volume";s:1:"0";s:12:"merchant_sic";s:0:"";s:16:"merchant_bustype";s:0:"";s:16:"merchant_ps_sold";s:11:"Wifi Device";s:17:"merchant_buslevel";s:4:"MOTO";s:10:"referer_id";s:0:"";s:10:"ref_p_type";s:0:"";s:11:"ref_p_value";s:0:"";s:9:"ref_p_pct";s:2:"80";s:11:"reseller_id";s:0:"";s:10:"res_p_type";s:0:"";s:11:"res_p_value";s:0:"";s:9:"res_p_pct";s:0:"";s:8:"group_id";s:0:"";s:9:"entity_id";s:0:"";}s:10:"AddressBus";a:6:{s:2:"id";s:36:"bb466091-b2ce-49e8-a22d-253efebc1b4f";s:11:"merchant_id";s:36:"dca93ecb-8309-494a-a555-1aa3cce27077";s:14:"address_street";s:21:"88 Piikoi St Apt 1102";s:12:"address_city";s:9:"Honolulu2";s:13:"address_phone";s:12:"808-457-5625";s:11:"address_fax";s:0:"";}s:13:"TimelineEntry";a:1:{i:0;a:4:{s:2:"id";s:36:"c2063126-5676-41dd-8e5f-1a2276dbed3f";s:11:"merchant_id";s:36:"dca93ecb-8309-494a-a555-1aa3cce27077";s:23:"timeline_date_completed";a:3:{s:5:"month";s:2:"03";s:3:"day";s:2:"06";s:4:"year";s:4:"2015";}s:16:"timeline_item_id";s:36:"a2f754b7-dbbc-4249-bd2c-d129815ac4c7";}}s:11:"saveOptions";a:2:{s:12:"associateLog";a:2:{s:5:"model";s:12:"MerchantNote";s:5:"field";s:15:"loggable_log_id";}s:4:"deep";b:1;}}	2015-12-01 11:34:40	\N
565d863d-13c8-44a8-bb72-03fbc0a8ad65	newChangeRequest	Merchant	00000000-0000-0000-0000-000000000003	8ef7f0d5-0c53-46fa-ac09-864868cbfdb7	a:5:{s:7:"pending";s:14:"Save for later";s:8:"Merchant";a:35:{s:12:"merchant_mid";s:16:"7641110042407299";s:2:"id";s:36:"dca93ecb-8309-494a-a555-1aa3cce27077";s:12:"merchant_dba";s:16:"Seeking Wifi LLC";s:14:"merchant_email";s:28:"reece.hirano@seekingwifi.com";s:12:"merchant_url";s:17:"Http://apwall.com";s:16:"merchant_contact";s:12:"Reece Hirano";s:25:"merchant_contact_position";s:7:"Manager";s:14:"reporting_user";s:0:"";s:7:"user_id";s:36:"a6029617-64a2-463d-be08-5fdcbfb5b0cd";s:10:"sm_user_id";s:0:"";s:11:"sm2_user_id";s:0:"";s:8:"CardType";s:0:"";s:10:"network_id";s:0:"";s:19:"back_end_network_id";s:0:"";s:20:"merchant_acquirer_id";s:0:"";s:15:"merchant_bin_id";s:0:"";s:20:"original_acquirer_id";s:0:"";s:14:"bet_network_id";s:36:"55cc6d99-d534-45f1-acc7-184d34627ad4";s:19:"cancellation_fee_id";s:0:"";s:10:"partner_id";s:0:"";s:22:"partner_exclude_volume";s:1:"0";s:12:"merchant_sic";s:0:"";s:16:"merchant_bustype";s:0:"";s:16:"merchant_ps_sold";s:11:"Wifi Device";s:17:"merchant_buslevel";s:4:"MOTO";s:10:"referer_id";s:0:"";s:10:"ref_p_type";s:0:"";s:11:"ref_p_value";s:0:"";s:9:"ref_p_pct";s:3:"100";s:11:"reseller_id";s:0:"";s:10:"res_p_type";s:0:"";s:11:"res_p_value";s:0:"";s:9:"res_p_pct";s:0:"";s:8:"group_id";s:0:"";s:9:"entity_id";s:0:"";}s:10:"AddressBus";a:6:{s:2:"id";s:36:"bb466091-b2ce-49e8-a22d-253efebc1b4f";s:11:"merchant_id";s:36:"dca93ecb-8309-494a-a555-1aa3cce27077";s:14:"address_street";s:21:"88 Piikoi St Apt 1102";s:12:"address_city";s:9:"Honolulu3";s:13:"address_phone";s:12:"808-457-5625";s:11:"address_fax";s:0:"";}s:13:"TimelineEntry";a:1:{i:0;a:4:{s:2:"id";s:36:"c2063126-5676-41dd-8e5f-1a2276dbed3f";s:11:"merchant_id";s:36:"dca93ecb-8309-494a-a555-1aa3cce27077";s:23:"timeline_date_completed";a:3:{s:5:"month";s:2:"03";s:3:"day";s:2:"06";s:4:"year";s:4:"2015";}s:16:"timeline_item_id";s:36:"a2f754b7-dbbc-4249-bd2c-d129815ac4c7";}}s:11:"saveOptions";a:2:{s:12:"associateLog";a:2:{s:5:"model";s:12:"MerchantNote";s:5:"field";s:15:"loggable_log_id";}s:4:"deep";b:1;}}	2015-12-01 11:36:29	\N
56698fed-a29c-414d-8298-5792c0a8ad65	newChangeRequest	Merchant	592b1d43-f312-44e9-8e4d-b8665ee212bc	8ef7f0d5-0c53-46fa-ac09-864868cbfdb7	a:38:{s:10:"AddressBus";a:7:{s:2:"id";s:36:"9161c9f0-b383-43f0-b850-87e847e08ec4";s:11:"merchant_id";s:36:"4e3587be-aafb-48c4-9b6b-8dd26b8e94aa";s:14:"address_street";s:31:"4880 South 131st Street Suite 3";s:12:"address_city";s:5:"Omaha";s:13:"address_phone";s:10:"4028892352";s:11:"address_fax";s:0:"";s:15:"address_type_id";s:36:"31e277ff-423a-4af8-9042-8310d7c320df";}s:13:"TimelineEntry";a:0:{}s:8:"CardType";a:1:{s:8:"CardType";s:0:"";}s:12:"merchant_mid";s:6:"352619";s:2:"id";s:36:"592b1d43-f312-44e9-8e4d-b8665ee212bc";s:12:"merchant_dba";s:16:"1010 on the Lake";s:14:"merchant_email";s:29:"payment.services@appfolio.com";s:12:"merchant_url";s:16:"www.appfolio.com";s:16:"merchant_contact";s:0:"";s:25:"merchant_contact_position";s:5:"Owner";s:14:"reporting_user";s:0:"";s:7:"user_id";s:36:"923c5e32-f579-44d4-8532-3f61116b9119";s:10:"sm_user_id";s:0:"";s:11:"sm2_user_id";s:0:"";s:10:"network_id";s:0:"";s:19:"back_end_network_id";s:0:"";s:20:"merchant_acquirer_id";s:0:"";s:15:"merchant_bin_id";s:0:"";s:20:"original_acquirer_id";s:0:"";s:14:"bet_network_id";s:36:"55cc6d99-d534-45f1-acc7-184d34627ad4";s:19:"cancellation_fee_id";s:0:"";s:10:"partner_id";s:0:"";s:22:"partner_exclude_volume";s:1:"0";s:12:"merchant_sic";s:4:"6513";s:16:"merchant_bustype";s:40:"Property Management Services and Rentals";s:16:"merchant_ps_sold";s:0:"";s:17:"merchant_buslevel";s:17:"EFTSecure VT Only";s:10:"referer_id";s:0:"";s:10:"ref_p_type";s:0:"";s:11:"ref_p_value";s:0:"";s:9:"ref_p_pct";s:0:"";s:11:"reseller_id";s:0:"";s:10:"res_p_type";s:0:"";s:11:"res_p_value";s:0:"";s:9:"res_p_pct";s:0:"";s:8:"group_id";s:0:"";s:9:"entity_id";s:0:"";s:11:"saveOptions";a:2:{s:12:"associateLog";a:2:{s:5:"model";s:12:"MerchantNote";s:5:"field";s:15:"loggable_log_id";}s:4:"deep";b:1;}}	2015-12-10 14:45:01	\N
67709fed-a29c-414d-8298-5792c0a8ad65	newChangeRequest	Merchant	592b1d43-f312-44e9-8e4d-b8665ee212bc	8ef7f0d5-0c53-46fa-ac09-864868cbfdb7	a:1:{s:11:"saveOptions";a:2:{s:12:"associateLog";a:2:{s:5:"model";s:12:"MerchantNote";s:5:"field";s:15:"loggable_log_id";}s:4:"deep";b:1;}}	2015-12-10 14:45:01	\N
\.


--
-- Data for Name: merchant_ach_app_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_ach_app_statuses (id, app_status_id_old, app_status_description, rank, app_true_price, app_rep_price, enabled) FROM stdin;
ec4a7409-0ad7-48be-9d83-08356f3b16fe	1	Online app + emailed S/K	4	20.0000	20.0000	t
fd5a7409-0ad7-48be-9d83-08356f3b16fe	2	Online app + shipped S/K	2	30.0000	30.0000	t
\.


--
-- Data for Name: merchant_ach_billing_options; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_ach_billing_options (id, billing_option_id_old, billing_option_description) FROM stdin;
a355e6ca-4167-48cb-8ef0-e8cf94104e99	2	Bill Rep his percentage
a6b2867a-0e6b-4059-a5d0-ea259baa99e4	3	Don't Bill Rep
00877702-ac70-4b9e-b3e8-518b651cdde7	1	Rep
5cdca7f1-8248-4a73-90c9-4bd434627ad4	\N	Client
5cdca7f1-53e8-4838-849e-4bd434627ad4	\N	Partner
\.


--
-- Data for Name: merchant_ach_reasons; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_ach_reasons (id, old_id, reason, "position", enabled, accounting_report_col_alias, non_taxable_reason_id) FROM stdin;
5cdf0bb4-8c14-4fb4-a34b-21fc0a050733	\N	Install	12	t	\N	\N
59b8777a-2d94-42df-8997-1c7f34627ad4	LFN	Lease Funding	19	f	\N	\N
59b8777a-c7f8-47a4-abb2-1c7f34627ad4	\N	Micros Billing	20	f	\N	\N
59b8777a-d580-41ce-924b-1c7f34627ad4	\N	Misc Fees	21	f	\N	\N
59b8777a-ea74-4215-8eb9-1c7f34627ad4	\N	PCI Non-Compliance	22	f	\N	\N
59b8777a-0288-4b12-b2ce-1c7f34627ad4	\N	Precidia Billing	23	f	\N	\N
59b8777a-7670-4521-a3ee-1c7f34627ad4	ADD	Additional Equipment Fee	24	f	\N	\N
5cba5b96-2034-46ca-8e89-78f80a0101f3	\N	ACH Setup	26	t	\N	\N
59b8777a-ffc4-40d6-b328-1c7f34627ad4	TAX	Tax	28	f	\N	\N
59b8777a-707c-4f7c-8c0b-1c7f34627ad4	\N	USAePay Billing	29	f	\N	\N
59b8777a-7ae4-496c-8d5f-1c7f34627ad4	\N	Voided Warrenty Fee	30	f	\N	\N
59b8777a-854c-450c-9559-1c7f34627ad4	\N	Wireless Activation	31	f	\N	\N
59b8777a-2dd0-4378-bd95-1c7f34627ad4	PRO	Processing Fees	32	f	\N	\N
59b8777a-a234-4ffe-bc4d-1c7f34627ad4	\N	Annual Fees	33	f	\N	\N
59b8777a-122c-45c1-813e-1c7f34627ad4	\N	AppFolio Billing	34	f	\N	\N
59b8777a-3148-408e-85f0-1c7f34627ad4	\N	Authorize.net Set Up	35	f	\N	\N
59b8777a-3f98-46b9-bbff-1c7f34627ad4	\N	Axia ePay Set Up	36	f	\N	\N
59b8777a-516c-4466-812b-1c7f34627ad4	\N	Billing for Rejects	37	f	\N	\N
59b8777a-6214-4be0-ba0b-1c7f34627ad4	\N	Conversion	38	f	\N	\N
59b8777a-6f38-4635-a570-1c7f34627ad4	\N	Data Breach Ins.	39	f	\N	\N
59b8777a-5cf8-47f3-bd0d-1c7f34627ad4	DDS	DDS Credit	40	f	\N	\N
59b8777a-80a8-4f7f-b936-1c7f34627ad4	\N	Encryption	41	f	\N	\N
59b8777a-91a4-4723-ab2d-1c7f34627ad4	SHP	General Shipping Fee (CA - Taxed)	42	f	\N	\N
59b8777a-a6a8-4dc0-a0af-1c7f34627ad4	\N	Giftcards (CA - Taxed)	43	f	\N	\N
59b8777a-b304-49be-8031-1c7f34627ad4	\N	Giftcards (Outside CA)	44	f	\N	\N
5cba5b96-16f8-4f0b-bc8e-78f80a0101f3	\N	ACH Setup	5	t	ach_setups	\N
59b8777a-483c-46b7-a6ee-1c7f34627ad4	APP	Application Fee	6	t	app_fees	\N
5cba5bdc-7778-4b66-8275-13f40a0101f3	\N	Reinjection Fee	4	t	equip_repair_fees	\N
59b8777a-9ab0-4b39-9255-1c7f34627ad4	\N	Equipment Fee	8	t	equip_sales	\N
59b8777a-b890-4ec2-8311-1c7f34627ad4	SUP	Supplies Fee (CA - Taxed)	9	t	equip_sales	\N
59b8777a-6290-4a70-8fed-1c7f34627ad4	\N	Supplies Fee (Outside CA)	10	t	equip_sales	\N
59b8777a-54e4-4fc9-88c5-1c7f34627ad4	REP	Replacement Fees	13	t	replace_fees	\N
5ce6cea3-bc3c-47e8-a102-3db10a050733	\N	Expedite Fee	3	t	expedite_fees	\N
59b8777a-2be8-4f26-931f-1c7f34627ad4	\N	Gateway/Software Fee	17	t	software_setup_fees	\N
5aecc84f-91d8-4de9-9613-3b2b0a0101f3	\N	Payment Fusion Terminal Setup Fee	27	t	licence_setup_fees	\N
5ce6cebe-5940-490c-980b-3db10a050733	\N	Account Setup Fee	1	t	account_setup_fees	\N
59b8777a-14b8-4ed8-8393-1c7f34627ad4	INS	Install	14	t	client_implement_fees	\N
59b8777a-ec60-490a-ab85-1c7f34627ad4	CNL	Cancellation Fee	11	t	termination_fees	\N
59b8777a-36b4-4004-b906-1c7f34627ad4	\N	Exact Shipping (No Tax)	18	t	shipping_fees	\N
5ce6ceb1-1418-4ae8-8bfa-3db10a050733	\N	Replacement Shipping	2	t	replacement_shipping_fees	\N
59b8777a-0e80-4352-bc53-1c7f34627ad4	\N	Reject Fees	25	t	reject_fees	\N
59b8777a-1d34-413a-8876-1c7f34627ad4	\N	Rental Fee (Outside CA)	16	t	rental_fees	\N
59b8777a-6974-4b33-bb72-1c7f34627ad4	REN	Rental Fee (CA - Taxed)	15	t	rental_fees	\N
59b8777a-8444-4c8a-adfb-1c7f34627ad4	OTH	Other	45	t	misc_fees	\N
\.


--
-- Data for Name: merchant_aches; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_aches (id, ach_seq_number_old, merchant_id, merchant_id_old, date_submitted, date_completed, reason, credit_amount, debit_amount, reason_other, status, user_id, user_id_old, invoice_number, app_status_id_old, merchant_ach_app_status_id, billing_option_id_old, merchant_ach_billing_option_id, depricated_commission_month, tax, merchant_ach_reason_id, ach_date, resubmit_date, rep_bill_amount, exact_shipping_amount, general_shipping_amount, ach_amount, total_ach, rejected, is_debit, ach_not_collected, user_compensation_profile_id, commission_month, commission_year, non_taxable_ach_amount, tax_state_name, tax_city_name, tax_county_name, tax_rate_state, tax_rate_county, tax_rate_city, tax_rate_district, tax_amount_state, tax_amount_county, tax_amount_city, tax_amount_district, ship_to_street, ship_to_city, ship_to_state, ship_to_zip, acctg_month, acctg_year, related_foreign_inv_number) FROM stdin;
c19d038b-b2e1-4b73-8362-67d9f5a6c40b	9763	7c2d713c-654b-4f4b-a6ce-51a3b5a2b383	3948000030002777	2007-06-25	2007-06-25	APP	\N	50.0000	\N	COMP	dedb3d8e-fad9-41d5-a21e-60651c5ab967	70	2007069763	2	ae9c848d-3448-430d-a8e6-6cb966c69e97	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	50.00	f	t	f	\N	\N	\N	50.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
6765ac8f-5844-457f-91d0-b0910a46c592	9782	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	3948000030002785	2007-06-25	2007-06-25	APP	\N	195.0000	\N	COMP	dedb3d8e-fad9-41d5-a21e-60651c5ab967	70	2007069782	2	ae9c848d-3448-430d-a8e6-6cb966c69e97	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	195.00	f	t	f	\N	\N	\N	195.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
25928313-8754-4716-8b53-3280e3b1a3f4	10473	77814d49-4029-42d8-bac5-92d44a22f9d6	3948000030002917	2007-08-21	2007-08-21	APP	\N	50.0000	\N	COMP	dedb3d8e-fad9-41d5-a21e-60651c5ab967	70	20070810473	2	ae9c848d-3448-430d-a8e6-6cb966c69e97	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	50.00	f	t	f	\N	\N	\N	50.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
e900ec8a-4741-4bc1-ba4b-dab816255843	13468	af28a757-c325-4e4b-a9bb-0e8b1634c629	3948906204000019	2008-08-04	2008-08-04	APP	\N	50.0000	\N	COMP	d65206c0-d5b5-4244-904c-f584f505012d	28	20080813468	6	4411b97f-deb3-4673-a671-090c02126571	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	50.00	f	t	f	\N	\N	\N	50.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
e845dd54-70b2-4b27-a61d-6c5c95af800b	27752	756b5550-912f-4d1c-b8a1-a21a80fc38ad	7641110042403579	2012-02-20	2012-02-20	APP	\N	125.0000	\N	COMP	14c01ae7-eda3-455f-96b6-eb19383fa971	159	20120227752	16	43a3f634-eb15-4a2e-bcdf-7f939b968f77	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	125.00	f	t	f	\N	\N	\N	125.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
3a96e01c-3c80-46fb-849e-18cd1f3afa41	33635	375a9eb2-f4c6-4790-b2e1-22d21f7e06f7	7641110042404832	2013-03-15	2013-03-15	APP	\N	195.0000	\N	COMP	98e5a7c5-c0f9-4258-9a0b-2647eaa61bd7	208	20130333635	24	2df5f57d-e611-4671-bc85-f04c5474597d	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	195.00	f	t	f	\N	\N	\N	195.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
8a341b34-50ab-420a-8f09-fe44f9967dbc	41996	65873e09-f556-41e6-9751-1811ae5e361d	7641110042406622	2014-07-25	2014-07-25	APP	\N	65.0000	\N	COMP	28e31b28-9040-4e0e-949d-90dd738d4b5c	199	20140741996	17	3b05908f-5bc6-4e28-8be4-19aaf77048d4	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	65.00	f	t	f	\N	\N	\N	65.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
56a6ba9a-2098-460f-97b3-ddbab6a6b17b	33834	375a9eb2-f4c6-4790-b2e1-22d21f7e06f7	7641110042404832	2013-03-25	2013-03-25	ADD	\N	595.0000	\N	COMP	2bf9820d-044f-45b5-ad5c-9b1d95acd409	60	20130333834	\N	\N	1	468dd26b-56e8-47ba-b414-807e6c084bb2	\N	\N	55104ce5-d7a8-40df-9575-225e34627ad4	\N	\N	\N	\N	\N	0	595.00	f	t	f	\N	\N	\N	595.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
db3a7409-0ad7-48be-9d83-08356f3b16fe	6142	cef25db7-3c95-470d-8015-af138ddf408e	3948000030001962	2006-07-03	2006-07-03	APP	\N	195.0000	Other reason	COMP	dedb3d8e-fad9-41d5-a21e-60651c5ab967	70	2006076142	2	ae9c848d-3448-430d-a8e6-6cb966c69e97	1	468dd26b-56e8-47ba-b414-807e6c084bb2	6:2006	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	195.00	f	t	f	\N	6	2006	195.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
a7b23a26-4c91-4f3c-95d9-741b54166e58	10810	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	3948000030003045	2007-10-01	2007-10-01	APP	\N	142.5000	\N	COMP	d65206c0-d5b5-4244-904c-f584f505012d	28	20071010810	1	1689b18c-2106-4802-9730-f67be144b5e4	1	468dd26b-56e8-47ba-b414-807e6c084bb2	9:2007	0.0000	55104ce5-e688-468e-ae95-225e34627ad4	\N	\N	\N	\N	\N	0	142.50	f	t	f	\N	9	2007	142.5000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
66a6ba9a-2098-460f-97b3-ddbab6a6b17b	43834	00000000-0000-0000-0000-000000000003	8641110042404832	2015-12-25	2015-12-25	TAX	0.0000	500.0000	\N	COMP	00000000-0000-0000-0000-000000000010	\N	\N	\N	\N	1	00877702-ac70-4b9e-b3e8-518b651cdde7	12:2015	\N	\N	2015-12-25	\N	\N	16.88	\N	0	\N	f	t	f	5570e7dc-a4fc-444c-8929-337a34627ad4	12	2015	500.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
76a6ba9a-2098-460f-97b3-ddbab6a6b17b	53834	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	8641110042404832	2015-12-25	2015-12-25	APP	0.0000	95.0000	\N	COMP	32165df7-6b97-4f86-9e6f-8638eb30cd9e	\N	\N	\N	ec4a7409-0ad7-48be-9d83-08356f3b16fe	1	00877702-ac70-4b9e-b3e8-518b651cdde7	12:2015	\N	\N	2015-12-25	\N	\N	\N	\N	0	\N	f	t	f	6570e7dc-a4fc-444c-8929-337a34627ad4	12	2015	95.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
86a6ba9a-2098-460f-97b3-ddbab6a6b17b	63834	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	9641110042404832	2015-12-25	2015-12-25	APP	0.0000	35.0000	\N	COMP	32165df7-6b97-4f86-9e6f-8638eb30cd9e	\N	\N	\N	fd5a7409-0ad7-48be-9d83-08356f3b16fe	1	00877702-ac70-4b9e-b3e8-518b651cdde7	12:2015	\N	\N	2015-12-25	\N	\N	\N	\N	0	\N	f	t	f	6570e7dc-a4fc-444c-8929-337a34627ad4	12	2015	35.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
96a6ba9a-2098-460f-97b3-ddbab6a6b17b	73834	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	9741110042404832	2015-12-25	2015-12-25	TAX	0.0000	500.0000	\N	COMP	43265df7-6b97-4f86-9e6f-8638eb30cd9e	123456	\N	\N	\N	1	00877702-ac70-4b9e-b3e8-518b651cdde7	12:2015	\N	\N	2015-12-25	\N	\N	16.88	\N	0	\N	f	t	f	7570e7dc-a4fc-444c-8929-337a34627ad4	12	2015	500.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: merchant_acquirers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_acquirers (id, acquirer_id_old, acquirer, reference_only) FROM stdin;
e8797a20-601d-4d1e-8a07-50266a013075	\N	TSYS	f
57efbef2-0ae4-4722-92fa-d7219b7fdda4	\N	FDC	f
4c501966-610f-4fe0-abc2-7a0245b292cf	\N	Direct Connect	f
5739a131-00cc-43b4-84ea-17c934627ad4	\N	Pivotal Canada	f
5739a131-0c60-4fcf-a761-17c934627ad4	\N	BAMS	f
7a270137-75d8-4a49-8734-3d03e087d1bd	\N	Sage Payments	f
192f8406-836d-4341-a1ee-d9d568cdb53d	\N	Axia	f
\.


--
-- Data for Name: merchant_banks; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_banks (id, merchant_id, merchant_id_old, bank_routing_number, bank_dda_number, bank_name, bank_routing_number_disp, bank_dda_number_disp, fees_routing_number, fees_dda_number, fees_routing_number_disp, fees_dda_number_disp) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	\N	\N	Lorem ipsum dolor sit amet	Lo	Lo	\N	\N	Lo	Lo
\.


--
-- Data for Name: merchant_bins; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_bins (id, bin_id_old, bin) FROM stdin;
55cc6d66-afd8-4a04-8ccb-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-bde8-4fec-9b49-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-c680-4e70-84c1-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-ce50-4020-9f68-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-d620-4c23-8ba2-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-ddf0-419d-8fcf-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-e5c0-4bf3-aa14-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-ed90-4972-bda6-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-f560-44c7-9446-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
55cc6d66-fd30-444b-8c3f-17f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor s
5806a480-7080-4856-9b65-319b34627ad4	\N	424550
44977880-7080-5555-9b65-449778627ad4	\N	449778
\.


--
-- Data for Name: merchant_cancellation_subreasons; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_cancellation_subreasons (id, id_old, name, visible) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	t
\.


--
-- Data for Name: merchant_cancellations; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_cancellations (id, merchant_id, merchant_id_old, date_submitted, date_completed, fee_charged, reason, status, axia_invoice_number, date_inactive, merchant_cancellation_subreason, merchant_cancellation_subreason_id, subreason_id_old, exclude_from_attrition) FROM stdin;
f918f38f-32c7-420b-bc5c-9ff78cccaf7f	43232fe0-16cd-4f86-a612-a6b83163d20a	\N	2004-06-18	1969-12-31	0.0000	never used account	COMP	\N	\N	\N	\N	\N	f
dca19fab-8133-4cef-8341-93da7f26ea3c	5598d3aa-d9d6-4a78-93fa-f0e701abed0d	\N	2005-05-06	2005-05-25	0.0000	fees rejecting	COMP	\N	\N	\N	\N	\N	f
0a30157d-4551-4ee2-86e8-72b8c94dac48	651513b1-a80e-4db5-81d0-7b6320e13bcf	\N	2006-02-09	2006-02-22	0.0000	changed ownership to LLC	COMP	\N	\N	\N	\N	\N	f
8f2bd96e-79ae-4a02-8d64-d5a1eda15d9f	c04518b7-4f3b-4bce-9a01-e6b014ded219	\N	2005-03-30	2005-03-31	0.0000	switched to Visanet	COMP	\N	\N	\N	\N	\N	f
392fea2f-7e0e-4eb8-a928-3270daebabf7	efbb4876-339b-421a-a0f5-1ff21312aebe	\N	2005-02-10	2005-02-23	195.0000	switched to Lynk, better rate	COMP	\N	\N	\N	\N	\N	f
00000000-0000-0000-0000-000000000006	00000000-0000-0000-0000-000000000004	\N	20014-02-10	20014-02-23	200.0000	No Reason at all	COMP	\N	\N	\N	\N	\N	f
\.


--
-- Data for Name: merchant_cancellations_histories; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_cancellations_histories (id, merchant_id, subreason_id, date_submitted, date_completed, date_inactive, date_reactivated, fee_charged, reason, merchant_cancellation_subreason, axia_invoice_number) FROM stdin;
\.


--
-- Data for Name: merchant_card_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_card_types (id, merchant_id, merchant_id_old, card_type_id, card_type_old) FROM stdin;
00000000-0000-0000-0000-000000000001	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	e9580d94-29ce-4de4-9fd4-c81d1afefbf4	\N
00000000-0000-0000-0000-000000000002	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	d3216cb3-ee71-40c6-bede-ac9818e24f3a	\N
00000000-0000-0000-0000-000000000003	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	ddea093f-ab15-44f6-87ba-fb4c4235ced1	\N
00000000-0000-0000-0000-000000000004	00000000-0000-0000-0000-000000000003	\N	ddea093f-ab15-44f6-87ba-fb4c4235ced1	\N
00000000-0000-0000-0000-000000000005	00000000-0000-0000-0000-000000000003	\N	d82f4282-f816-4880-975c-53d42a7f02bc	\N
\.


--
-- Data for Name: merchant_changes; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_changes (id, change_id_old, change_type_id, change_type_old, merchant_id, merchant_id_old, user_id, user_id_old, status, date_entered, time_entered, date_approved, time_approved, change_data, merchant_note_id, merchant_note_id_old, approved_by_user_id, approved_by_old, equipment_programming_id, programming_id_old) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	Lor	2012-08-15	12:48:46	2012-08-15	12:48:46	Lorem ipsum dolor sit amet, aliquet feugiat.	00000000-0000-0000-0000-000000000001	\N	\N	\N	\N	\N
\.


--
-- Data for Name: merchant_gateways_archives; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_gateways_archives (id, gateway1_pricing_id, merchant_id, gateway_id, user_id, month, year, referer_id, reseller_id, ref_p_type, ref_p_value, res_p_type, res_p_value, ref_p_pct, res_p_pct, r_rate_pct, r_per_item_fee, r_statement_fee, m_rate_pct, m_per_item, m_statement) FROM stdin;
\.


--
-- Data for Name: merchant_notes; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_notes (id, merchant_note_id_old, note_type_id_old, note_type_id, user_id_old, user_id, merchant_id_old, merchant_id, note_date, note, note_title, general_status, date_changed, critical, note_sent, loggable_log_id, resolved_date, resolved_time, change_id_old, approved_by_user_id) FROM stdin;
3d021f7d-c424-4971-bacc-eaea20dc3d75	\N	\N	a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2	\N	11b9a426-800d-46ff-8ab7-f8695a55640d	\N	df999cf7-44fb-4183-8fe5-48d03a5a8708	2006-06-08 00:00:00	removed any rep	Account Information Change	\N	2006-06-08	0	\N	\N	\N	\N	\N	\N
fb71fc05-5c47-49a7-8c7c-86ff1074d268	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	429e22f3-8a23-4fcc-a13e-14698ee21303	\N	9f3c9149-6301-4b2b-873a-0d077999497d	2010-03-09 00:00:00	Sage transferred Bobbie to us. The merchant was notified by their bank that $25 was attempting to be debited from their account. When I asked Bobbie for the line item description he read off a long list of numbers. He said that the originator phone number his bank provided said that the originator was based out of Boise, ID. I verified in TSYS and Virtual Reports to confirm that he was not being billed. I called Bobbie on his cell phone 208-720-2882 to inform him of the note on the account stating that the merchant pays a $25 monthly terminal fee to MSC.	Bobbie called	COMP	2010-03-10	0	0	\N	\N	\N	\N	\N
00000000-0000-0000-0000-000000000003	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	047296f8-cdde-4e48-bd73-9094b8415bbc	\N	00000000-0000-0000-0000-000000000003	2006-04-17 00:00:00	Merchant pays $25 monthly for equipment rental.	MSC Rental	COMP	2006-04-17	1	\N	\N	\N	\N	\N	\N
7bd82606-f303-42ea-a9b8-421317ff5846	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	047296f8-cdde-4e48-bd73-9094b8415bbc	\N	9f3c9149-6301-4b2b-873a-0d077999497d	2006-04-17 00:00:00	Merchant pays $25 monthly for equipment rental.	MSC Rental	COMP	2006-04-17	1	\N	\N	\N	\N	\N	\N
d8dd1943-4c6f-4c6b-99b5-4ebcbc56447e	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	80dfe4f9-393d-4bae-965a-68280e6038e7	\N	9f3c9149-6301-4b2b-873a-0d077999497d	2004-07-01 00:00:00	received letter to close this account, will remain processing under other account.  Submitting request.	cancellation	COMP	2004-07-01	0	\N	\N	\N	\N	\N	\N
0194fa5c-76b9-4e77-b5d5-1d276ca88e5b	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	80dfe4f9-393d-4bae-965a-68280e6038e7	\N	9f3c9149-6301-4b2b-873a-0d077999497d	2004-06-21 00:00:00	Received letter and submitting request.  Would like FedEx label to ship us their terminal.	Cancellation	COMP	2004-06-21	0	\N	\N	\N	\N	\N	\N
25ed9da6-75f3-4d1b-a6ea-1eecc86c86cd	\N	\N	a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2	\N	11b9a426-800d-46ff-8ab7-f8695a55640d	\N	9f3c9149-6301-4b2b-873a-0d077999497d	2004-01-09 00:00:00	added Bet	Bankcard Change	\N	2004-01-09	0	\N	\N	\N	\N	\N	\N
da968c33-6c1d-4582-9593-317984ca4d1c	\N	\N	a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2	\N	7da107ab-efda-4a39-8520-b61991bbe48a	\N	9f3c9149-6301-4b2b-873a-0d077999497d	2003-12-16 00:00:00	added amex	Bankcard T&E Change	\N	2003-12-16	0	\N	\N	\N	\N	\N	\N
f93765ff-d0e7-464e-937d-70146975d030	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	52fb2b43-6556-495c-8348-485b98ab08d1	\N	9f3c9149-6301-4b2b-873a-0d077999497d	2003-12-11 00:00:00	Confirmed Pricing	Confirmed Pricing	COMP	2003-12-11	0	\N	\N	\N	\N	\N	\N
89207724-c2e1-4d46-ae3c-2085d97bbb55	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	047296f8-cdde-4e48-bd73-9094b8415bbc	\N	d865f2bb-bc63-4839-a548-49f196beee66	2006-04-05 00:00:00	I spoke to Lynn Toneri about the Toneri Gallery account and she mentioned they don't use this MM file.  I instructed her to fax in a letter.	This account is being closed	COMP	2006-04-05	0	\N	\N	\N	\N	\N	\N
5c4e56fc-3e42-4bd0-be12-0e0cdfae1c09	\N	\N	a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2	\N	047296f8-cdde-4e48-bd73-9094b8415bbc	\N	d865f2bb-bc63-4839-a548-49f196beee66	2005-10-05 00:00:00	Added cell number.	Account Information Change	\N	2005-10-05	0	\N	\N	\N	\N	\N	\N
00000000-0000-0000-0000-000000000012	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	047296f8-cdde-4e48-bd73-9094b8415bbc	\N	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	2005-10-05 00:00:00	General Note PEND Description	General Note PEND Title	PEND	2005-10-05	0	\N	\N	\N	\N	\N	\N
00000000-0000-0000-0000-000000000013	\N	\N	a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2	\N	047296f8-cdde-4e48-bd73-9094b8415bbc	\N	00000000-0000-0000-0000-000000000004	2005-10-05 00:00:00	Change request PEND Description	Change request PEND Title	PEND	2005-10-05	0	\N	\N	\N	\N	\N	\N
00000000-0000-0000-0000-000000000014	\N	\N	a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2	\N	113114ae-da7b-4970-94bd-fa0c0f25c786	\N	00000000-0000-0000-0000-000000000004	2005-10-05 00:00:00	Change request COMP Description	Change request COMP Title	COMP	\N	0	\N	67709fed-a29c-414d-8298-5792c0a8ad65	\N	\N	\N	\N
00000000-0000-0000-0000-000000000015	\N	\N	0bfee249-5c37-417c-aec7-83dcd2b2f566	\N	113114ae-da7b-4970-94bd-fa0c0f25c786	\N	00000000-0000-0000-0000-000000000004	2005-10-05 00:00:00	Change request COMP Description	Change request COMP Title	COMP	\N	0	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: merchant_owners; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_owners (id, owner_id_old, merchant_id, merchant_id_old, owner_social_sec_no, owner_equity, owner_name, owner_title, owner_social_sec_no_disp) FROM stdin;
00000000-0000-0000-0000-000000000002	967	00000000-0000-0000-0000-000000000003	merchant-3-old	tdGYA+LVUNxE4dHOjlpt6EDVJ0d8C5w0rmXH0ZozHTV/TgRE8t4i/7W6PJw0VijjArG5FVFXU+hQu3cpTcg3KQ==	\N	Owner Name 2	Owner title 2	4321
00000000-0000-0000-0000-000000000001	867	00000000-0000-0000-0000-000000000003	merchant-3-old	tdGYA+LVUNxE4dHOjlpt6D9c0KaeguumZkSSnzvvAjMPbvWWqG99+WpPF0EOQ8gazzIX/LtWCuuWIsrd2bmrAw==	\N	Owner Name 1	Owner title 1	6789
00000000-0000-0000-0000-000000000004	321	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	merchant-4-old	tdGYA+LVUNxE4dHOjlpt6EDVJ0d8C5w0rmXH0ZozHTV/TgRE8t4i/7W6PJw0VijjArG5FVFXU+hQu3cpTcg3KQ==	40	Owner Name 2	Owner title 2	4321
00000000-0000-0000-0000-000000000003	123	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	merchant-4-old	tdGYA+LVUNxE4dHOjlpt6EDVJ0d8C5w0rmXH0ZozHTV/TgRE8t4i/7W6PJw0VijjArG5FVFXU+hQu3cpTcg3KQ==	60	Owner Name 1	Owner title 1	4321
\.


--
-- Data for Name: merchant_pcis; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_pcis (id, merchant_id, merchant_id_old, compliance_level, saq_completed_date, compliance_fee, insurance_fee, last_security_scan, scanning_company, pci_enabled, saq_type, cs_board_request_guid, cs_cancel_request_guid, cancelled_controlscan, cancelled_controlscan_date) FROM stdin;
4167fda9-c1b0-405f-a3ff-93ced026fe2b	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	4	\N	\N	6.9500	\N	Control Scan	f	\N	\N	\N	f	\N
4167fda9-c1b0-405f-a3ff-000000000001	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	4	\N	\N	6.9500	\N	Control Scan	f	\N	\N	\N	f	\N
\.


--
-- Data for Name: merchant_pricing_archives; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_pricing_archives (id, merchant_id, user_id, acquirer_id, provider_id, products_services_type_id, month, year, m_rate_pct, m_per_item_fee, m_discount_item_fee, m_statement_fee, bet_table_id, bet_network_id, months_processing, interchange_expense, product_profit, gateway_mid, generic_product_mid, bet_extra_pct, original_m_rate_pct) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000003	5c525016-cb3f-424b-a402-b5593faa2ad7	\N	\N	e8fa66a0-790f-4710-b7de-ef79be75a1c7	3	2015	0.0000	0.0850	0.0000	12.5000	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: merchant_pricings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_pricings (id, merchant_id, gateway_id, mc_bet_table_id, visa_bet_table_id, ds_bet_table_id, ds_processing_rate, processing_rate, mc_vi_auth, ds_auth_fee, billing_mc_vi_auth, billing_discover_auth, billing_amex_auth, billing_debit_auth, discount_item_fee, amex_auth_fee, aru_voice_auth_fee, wireless_auth_fee, wireless_auth_cost, num_wireless_term, per_wireless_term_cost, total_wireless_term_cost, statement_fee, min_month_process_fee, ebt_auth_fee, ebt_access_fee, ebt_processing_rate, gateway_access_fee, wireless_access_fee, debit_auth_fee, debit_processing_rate, debit_access_fee, debit_acquirer_id, annual_fee, chargeback_fee, amex_processing_rate, amex_per_item_fee, cr_gateway_fees_separate, cr_gateway_rate_cost, cr_gateway_item_cost, cr_gateway_monthly_cost, pyc_charge_to_merchant, pyc_merchant_rebate, syc_charge_to_merchant, debit_discount_item_fee, ebt_discount_item_fee, ebt_acquirer_id, features, billing_ebt_auth, ds_user_not_paid, db_bet_table_id, ebt_bet_table_id, amex_bet_table_id, mc_acquirer_fee) FROM stdin;
550d54d9-afa8-49f5-aa32-0d8c34627ad4	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	558ade3b-f5ec-43e1-bd89-20ba34627ad4	558ade3b-f5ec-43e1-bd89-20ba34627ad4	558ade3b-f5ec-43e1-bd89-20ba34627ad4	558ade3b-f5ec-43e1-bd89-20ba34627ad4	\N	0.4500	0.1900	\N	0.29	0.1	0.3	0.1	\N	0.2000	0.6500	0.1000	0.10	1	15	15	10.0000	25.0000	\N	25	\N	0.0000	15.0000	\N	\N	20	7b897a42-5474-4dff-9c2d-d4c31db37270	159.0000	15.0000	\N	\N	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	\N	\N	558ade3b-f5ec-43e1-bd89-20ba34627ad4	0
550d54d9-7850-4fee-a292-0d8c34627ad4	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000002	\N	\N	0.2000	0.2400	\N	0.34	0.1	0.19	0.1	\N	0.0900	0.1000	0.1000	0.10	1	15	15	5.0000	0.0000	\N	\N	\N	0.0000	15.0000	\N	\N	\N	\N	95.0000	15.0000	\N	\N	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	0
00000000-0000-0000-0000-000000000001	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000002	\N	\N	0.2000	0.2400	\N	0.34	0.1	0.19	0.1	\N	0.0900	0.1000	0.1000	0.10	1	15	15	5.0000	0.0000	\N	\N	\N	0.0000	15.0000	\N	\N	\N	\N	95.0000	15.0000	\N	\N	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	0
00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000003	\N	00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000002	\N	\N	0.2000	0.2400	\N	0.34	0.1	0.19	0.1	\N	0.0900	0.1000	0.1000	0.10	1	15	15	5.0000	0.0000	\N	\N	\N	0.0000	15.0000	\N	\N	\N	\N	95.0000	15.0000	\N	\N	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	0
00000000-0000-0000-0000-000000000003	00000000-0000-0000-0000-000000000004	\N	00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000002	\N	\N	0.2000	0.2400	\N	0.34	0.1	0.19	0.1	\N	0.0900	0.1000	0.1000	0.10	1	15	15	5.0000	0.0000	\N	\N	\N	0.0000	15.0000	\N	\N	\N	\N	95.0000	15.0000	\N	\N	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	0
\.


--
-- Data for Name: merchant_references; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_references (id, merchant_ref_seq_number_old, merchant_id, merchant_id_old, merchant_reference_type_id, merchant_ref_type_old, business_name, person_name, phone) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	\N	\N	\N	Lorem ipsum dolor sit amet	Lorem ipsum dolor
\.


--
-- Data for Name: merchant_reject_lines; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_reject_lines (id, id_old, reject_id_old, merchant_reject_id, fee, status_id_old, merchant_reject_status_id, status_date, notes) FROM stdin;
55637fb7-d2fc-46ab-910b-26f134627ad4	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000020	1341.1200	Lorem ipsum dolor sit amet	a4961985-c384-43df-b03d-b269f560d1f5	2015-05-25	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
55637fb7-d2fc-46ab-910b-26f134627111	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000001	100.5000	Lorem ipsum dolor sit amet	4c82df9f-403b-4a8d-83e0-f85ed4b074d5	2015-05-25	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
55637fb7-d2fc-46ab-910b-26f134627222	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000001	200.7500	Lorem ipsum dolor sit amet	dd2123a3-aa3a-4747-bcfb-9b7641e48bc7	2015-05-25	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
\.


--
-- Data for Name: merchant_reject_recurrances; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_reject_recurrances (id, id_old, name) FROM stdin;
4aa0cdb2-afd4-42f2-beae-df9d39fc786b	1	1st Reject
b1e9eaea-1a03-4335-a6ac-e59d049ad451	2	Additional Reject
\.


--
-- Data for Name: merchant_reject_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_reject_statuses (id, id_old, name, collected, priority) FROM stdin;
a4961985-c384-43df-b03d-b269f560d1f5	1	Received	f	1
4c82df9f-403b-4a8d-83e0-f85ed4b074d5	2	Re-submitted	f	2
e683ee7e-97d2-4882-a5d5-bd79d5246a9e	3	Additional Reject	f	3
c0a5e7f2-5634-4d8d-81a2-ff43a33abe03	4	Updated Banking (NOC)	f	4
03fcd6ef-726b-40c4-8942-764d8bf6bc46	5	Re-submitted - Confirmed	t	5
dd2123a3-aa3a-4747-bcfb-9b7641e48bc7	6	Collected - Reserve	t	6
c109f205-9d85-445d-8010-c0013b8f6141	7	Collected - Other	t	7
79d18238-e403-4596-a37b-d7d4468795df	8	On Reserve	f	8
afa257c3-9dcb-4661-8946-91805171510c	9	Re-Rejected	f	9
87828851-cde7-4ddc-b0b9-213435caf2e8	10	Not Collected	f	10
\.


--
-- Data for Name: merchant_reject_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_reject_types (id, id_old, name) FROM stdin;
8b48f7ef-6115-41c0-9bbe-bcd34f786506	1	Reject
c70ac383-0e29-4394-9b75-9ca6c01200f7	2	Update Banking
\.


--
-- Data for Name: merchant_rejects; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_rejects (id, id_old, merchant_id, merchant_id_old, trace, reject_date, merchant_reject_type_id, type_id_old, code, amount, merchant_reject_recurrance_id, recurrance_id_old, open, loss_axia, loss_mgr1, loss_mgr2, loss_rep) FROM stdin;
00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	5678365	2015-05-15	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	Lorem i	100.0000	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	t	10.0000	20.0000	30.0000	40.0000
00000000-0000-0000-0000-000000000002	old2	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	2222	2015-05-21	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	Lorem i	100.0000	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	t	10.0000	20.0000	30.0000	40.0000
\.


--
-- Data for Name: merchant_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_types (id, type_description) FROM stdin;
267007da-e0ff-4866-b7ef-264571d29df8	ACH
07afdbac-d63a-4a69-90fd-8732223bcf8b	Acquiring
7886b254-4b38-41b0-9d87-ed409cfe549f	CareCredit
5f12c0ca-cf11-4033-b335-7e487aef02e5	Gateway
caee3415-d37e-474f-af62-1565c04b043c	Text&Pay
\.


--
-- Data for Name: merchant_uw_final_approveds; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_uw_final_approveds (id, merchant_uw_final_approved_id_old, name, active) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor s	t
\.


--
-- Data for Name: merchant_uw_final_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_uw_final_statuses (id, id_old, name) FROM stdin;
2fb6e87b-bf77-4d3c-96db-f12e9147fa5a	\N	Approved
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor s
\.


--
-- Data for Name: merchant_uw_volumes; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_uw_volumes (id, merchant_uw_id, merchant_id, mc_vi_ds_risk_assessment, mo_volume, average_ticket, max_transaction_amount, sales, ds_volume, pin_debit_volume, pin_debit_avg_ticket, amex_volume, amex_avg_ticket, card_present_swiped, card_present_imprint, card_not_present_keyed, card_not_present_internet, direct_to_consumer, direct_to_business, direct_to_government, te_amex_number, te_diners_club_number, te_discover_number, te_jcb_number, te_amex_number_disp, te_diners_club_number_disp, te_discover_number_disp, te_jcb_number_disp, mc_volume, visa_volume, discount_frequency, fees_collected_daily, next_day_funding) FROM stdin;
55cc6874-c6fc-48c4-874a-153134627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	0.00	0.00	0.00	0.00	1	0.00	0.00	0.00	0.00	0.00	0.00	0.00	0.00	0.00	0.00	0.00	0.00	\N	\N	\N	\N	Lo	Lo	Lo	Lo	0	0	\N	f	f
\.


--
-- Data for Name: merchant_uws; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchant_uws (id, merchant_id, merchant_id_old, tier_assignment, credit_pct, chargeback_pct, merchant_uw_final_status_id, final_status_id_old, merchant_uw_final_approved_id, final_approved_id_old, final_date, final_notes, mcc, expedited, ebt_risk_assessment, debit_risk_assessment, sponsor_bank_id, credit_score, funding_delay_sales, funding_delay_credits, app_quantity_type) FROM stdin;
00000000-0000-0000-0000-000000000001	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	1	1.0000	1.0000	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	2015-12-25	 rhoncus duis vestibulum nunc mattis convallis.	Lorem ipsum dolor sit amet	t	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: merchants; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.merchants (id, merchant_id_old, user_id, user_id_old, merchant_mid, merchant_name, merchant_dba, merchant_contact, merchant_email, merchant_ownership_type, merchant_tin, merchant_d_and_b, inactive_date, active_date, referer_id, ref_seq_number_old, network_id, network_id_old, merchant_buslevel, merchant_sic, entity_old, entity_id, group_id, group_id_old, merchant_bustype, merchant_url, merchant_tin_disp, merchant_d_and_b_disp, active, cancellation_fee_id, cancellation_fee_id_old, merchant_contact_position, merchant_mail_contact, reseller_id, res_seq_number_old, merchant_bin_id, merchant_bin_id_old, merchant_acquirer_id, merchant_acquirer_id_old, reporting_user, merchant_ps_sold, ref_p_type, ref_p_value, res_p_type, res_p_value, ref_p_pct, res_p_pct, onlineapp_application_id, partner_id, partner_id_old, partner_exclude_volume, aggregated, cobranded_application_id, product_group_id, original_acquirer_id, bet_network_id, back_end_network_id, sm_user_id, sm2_user_id, brand_id, womply_status_id, womply_merchant_enabled, womply_customer, region_id, subregion_id, organization_id, source_of_sale, is_acquiring_only, is_pf_only, general_practice_type, specific_practice_type, client_id, chargebacks_email, merchant_type_id) FROM stdin;
00000000-0000-0000-0000-000000000005	4541619030000153	00000000-0000-0000-0000-000000000004	555	4541619030000153	\N	Inactive merchant	None	inactive@example.com	Corporation	\N	\N	2012-08-12	2010-09-17	\N	\N	\N	5	Retail	5999	AX	8aa936ff-143c-47a7-bdd9-929acf3d2c15	\N	\N	Glass, metal, pottery, wood gifts, frames, jewelry, clothing	www.example.com	3165	\N	0	c673b1b4-1c98-4f82-9f54-c82001fb28cd	2	\N	\N	\N	\N	21664e0c-24cb-4064-ad1d-8a8b451e66b9	1	68b66eb9-364b-42a3-880d-68fadd73909e	1	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	f	\N	\N	\N	55cc6d99-e2e0-4119-8f62-184d34627ad4	\N	\N	\N	\N	\N	f	f	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	07afdbac-d63a-4a69-90fd-8732223bcf8b
3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	3948000030002785	32165df7-6b97-4f86-9e6f-8638eb30cd9e	36	3948000030002785	\N	Square One Internet Solutions	Adam	adam@startatsquareone.com	Corporation - LLC	tdGYA+LVUNxE4dHOjlpt6BMfo7javbZWrRQwXzy6gHSmcfFTTeaappKiTaAI29HtP8Vd/vqCDHED4rKj8UUZQA==	\N	2010-10-26	2007-06-25	\N	\N	4ddd04f1-9b70-4d4e-8a6c-6830a10c2cad	5	EFTSecure VT Only	7399	AX	39332ee6-cc48-4f56-8098-5236201b0215	\N	\N	BUSINESS SERVICES (NOT ELSEWHE	www.startatsquareone.com	3992	\N	1	e754fe22-a59a-4bf8-9f3f-f9f037e6fe9e	2	\N	\N	\N	\N	bc9188fd-b6c5-4d31-ba4e-551db8f0cbec	2	71646a03-93e9-4275-9f15-5f79b4a64179	2	\N	\N	\N	\N	\N	\N	\N	\N	\N	32165df7-6b97-4f86-9e6f-8638eb30cd9e	\N	f	f	\N	8d4f646c-6635-4e88-a2cf-31a984b5b2a2	\N	55104e48-942c-44e4-8aec-225e34627ad4	\N	\N	\N	\N	\N	f	f	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	07afdbac-d63a-4a69-90fd-8732223bcf8b
4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	3948000030003045	32165df7-6b97-4f86-9e6f-8638eb30cd9e	36	3948000030003045	\N	Refer Aloha, LLC	Charles	charles.burch@referaloha.com	Corporation - LLC	tdGYA+LVUNxE4dHOjlpt6FudqGgLTGBfaSZmH6R5BqXPR0Xb2rEuIaYFm6Fljr0C4SfRB2SngK0HYXPgo+7MmA==	\N	2009-11-16	2007-10-01	\N	\N	4ddd04f1-9b70-4d4e-8a6c-6830a10c2cad	5	EFTSecure VT Only	7311	AX	39332ee6-cc48-4f56-8098-5236201b0215	\N	\N	ADVERTISING SERVICES	www.referaloha.com	4192	\N	1	e754fe22-a59a-4bf8-9f3f-f9f037e6fe9e	2	\N	\N	\N	\N	bc9188fd-b6c5-4d31-ba4e-551db8f0cbec	2	71646a03-93e9-4275-9f15-5f79b4a64179	2	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	f	\N	4839a9b6-1299-4b8d-9540-399c99ab4a19	\N	55104e48-942c-44e4-8aec-225e34627ad4	\N	\N	\N	\N	\N	f	f	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	07afdbac-d63a-4a69-90fd-8732223bcf8b
00000000-0000-0000-0000-000000000003	3948000030003049	32165df7-6b97-4f86-9e6f-8638eb30cd9e	36	3948000030003049	\N	Another Merchant	Charles	charles.burch@example.com	Corporation - LLC	tdGYA+LVUNxE4dHOjlpt6FudqGgLTGBfaSZmH6R5BqXPR0Xb2rEuIaYFm6Fljr0C4SfRB2SngK0HYXPgo+7MmA==	\N	2009-11-16	2007-10-01	\N	\N	4ddd04f1-9b70-4d4e-8a6c-6830a10c2cad	5	EFTSecure VT Only	7311	AX	39332ee6-cc48-4f56-8098-5236201b0215	\N	\N	ADVERTISING SERVICES	www.example.com	4192	\N	1	e754fe22-a59a-4bf8-9f3f-f9f037e6fe9e	2	\N	\N	\N	\N	bc9188fd-b6c5-4d31-ba4e-551db8f0cbec	2	71646a03-93e9-4275-9f15-5f79b4a64179	2	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	f	\N	\N	\N	55104e48-942c-44e4-8aec-225e34627ad4	\N	\N	\N	\N	\N	f	f	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	07afdbac-d63a-4a69-90fd-8732223bcf8b
5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	4048000030003049	43265df7-6b97-4f86-9e6f-8638eb30cd9e	46	4048000030003049	\N	Aloha Burger	Joe	jjenkins@example.com	Corporation - LLC	tdGYA+LVUNxE4dHOjlpt6FudqGgLTGBfaSZmH6R5BqXPR0Xb2rEuIaYFm6Fljr0C4SfRB2SngK0HYXPgo+7MmA==	\N	\N	2007-10-01	\N	\N	4ddd04f1-9b70-4d4e-8a6c-6830a10c2cad	5	EFTSecure VT Only	7311	AX	39332ee6-cc48-4f56-8098-5236201b0215	\N	\N	Restaurant	www.example.com	4192	\N	1	\N	\N	\N	\N	\N	\N	bc9188fd-b6c5-4d31-ba4e-551db8f0cbec	2	71646a03-93e9-4275-9f15-5f79b4a64179	2	\N	\N	\N	\N	\N	\N	\N	\N	\N	43265df7-6b97-4f86-9e6f-8638eb30cd9e	\N	\N	\N	\N	c0293488-57c4-4f29-9259-b2d66ab6edf1	\N	55104e48-942c-44e4-8aec-225e34627ad4	\N	\N	\N	\N	\N	f	f	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	07afdbac-d63a-4a69-90fd-8732223bcf8b
00000000-0000-0000-0000-000000000004	3948906204000946	00ccf87a-4564-4b95-96e5-e90df32c46c1	123	3948906204000946	\N	16 Hands	Jill Damon	jill@example.com	Corporation	tdGYA+LVUNxE4dHOjlpt6OH7MYdYWa1McQSsepiyErJAJo7tqyzOKbSLvUEiG5SAtN8dkbjYNVyyP0fvk06FBw==	\N	2012-10-12	2009-07-20	\N	\N	\N	5	Retail	5999	AX	8aa936ff-143c-47a7-bdd9-929acf3d2c15	\N	\N	Glass, metal, pottery, wood gifts, frames, jewelry, clothing	www.example.com	3165	\N	1	c673b1b4-1c98-4f82-9f54-c82001fb28cd	2	\N	\N	\N	\N	21664e0c-24cb-4064-ad1d-8a8b451e66b9	1	68b66eb9-364b-42a3-880d-68fadd73909e	1	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	f	\N	\N	\N	55cc6d99-e2e0-4119-8f62-184d34627ad4	\N	\N	\N	\N	\N	f	f	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	07afdbac-d63a-4a69-90fd-8732223bcf8b
\.


--
-- Data for Name: networks; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.networks (id, network_id_old, network_description, "position") FROM stdin;
4ae4fb7d-d989-4662-b060-e7b0190bd7cf	\N	VISANET	1
01940ba8-3135-4203-ae33-a23a2cf8697b	\N	PTI	2
620a6eb1-8ead-42b6-8478-b1556df625a4	\N	ENVOY	3
666ea487-76cb-4721-bfdb-b12e3c1c0e09	\N	FD North	4
aff88e90-7fc3-4609-ad04-fc7ae2868299	\N	FD Omaha	5
\.


--
-- Data for Name: non_taxable_reasons; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.non_taxable_reasons (id, reason) FROM stdin;
5cf17a0f-0ad0-4126-bc5a-6d5634627ad4	Service/Non Equipment
5cf17a0f-68d0-48d3-9222-6d5634627ad4	Reseller
5cf17a0f-9598-41c3-a88f-6d5634627ad4	Out of State
5cf17a0f-a960-4514-b953-6d5634627ad4	Exact Shipping
\.


--
-- Data for Name: note_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.note_types (id, note_type_id_old, note_type_description) FROM stdin;
a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2	CHG	Change Request
0bfee249-5c37-417c-aec7-83dcd2b2f566	GNR	General Note
083e3d46-76f2-42d9-ad7b-6fb3a9775b3c	PRG	Programming Note
85b32624-aca2-44f2-9924-49cddc6b2e5a	INS	Installation & Setup Note
\.


--
-- Data for Name: old_bet_tables_card_types_backup; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.old_bet_tables_card_types_backup (id, bet_table_id, card_type_id) FROM stdin;
\.


--
-- Data for Name: old_bets_backup; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.old_bets_backup (id, network_id, user_id, bet_tables_card_types_id, bc_cost, bc_pi, csv_cost, csv_pi, add_pct) FROM stdin;
\.


--
-- Data for Name: onlineapp_api_logs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_api_logs (id, user_id, user_token, ip_address, request_string, request_url, request_type, created, auth_status) FROM stdin;
\.


--
-- Data for Name: onlineapp_apips; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_apips (id, user_id, ip_address) FROM stdin;
1	1	127.0.0.1
\.


--
-- Data for Name: onlineapp_applications; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_applications (id, user_id, status, hash, rs_document_guid, ownership_type, legal_business_name, mailing_address, mailing_city, mailing_state, mailing_zip, mailing_phone, mailing_fax, federal_taxid, corp_contact_name, corp_contact_name_title, corporate_email, loc_same_as_corp, dba_business_name, location_address, location_city, location_state, location_zip, location_phone, location_fax, customer_svc_phone, loc_contact_name, loc_contact_name_title, location_email, website, bus_open_date, length_current_ownership, existing_axia_merchant, current_mid_number, general_comments, location_type, location_type_other, merchant_status, landlord_name, landlord_phone, business_type, products_services_sold, return_policy, days_until_prod_delivery, monthly_volume, average_ticket, highest_ticket, current_processor, card_present_swiped, card_present_imprint, card_not_present_keyed, card_not_present_internet, method_total, direct_to_customer, direct_to_business, direct_to_govt, products_total, high_volume_january, high_volume_february, high_volume_march, high_volume_april, high_volume_may, high_volume_june, high_volume_july, high_volume_august, high_volume_september, high_volume_october, high_volume_november, high_volume_december, moto_storefront_location, moto_orders_at_location, moto_inventory_housed, moto_outsourced_customer_service, moto_outsourced_shipment, moto_outsourced_returns, moto_outsourced_billing, moto_sales_methods, moto_billing_monthly, moto_billing_quarterly, moto_billing_semiannually, moto_billing_annually, moto_policy_full_up_front, moto_policy_days_until_delivery, moto_policy_partial_up_front, moto_policy_partial_with, moto_policy_days_until_final, moto_policy_after, bank_name, bank_contact_name, bank_phone, bank_address, bank_city, bank_state, bank_zip, depository_routing_number, depository_account_number, same_as_depository, fees_routing_number, fees_account_number, trade1_business_name, trade1_contact_person, trade1_phone, trade1_acct_num, trade1_city, trade1_state, trade2_business_name, trade2_contact_person, trade2_phone, trade2_acct_num, trade2_city, trade2_state, currently_accept_amex, existing_se_num, want_to_accept_amex, want_to_accept_discover, term1_quantity, term1_type, term1_provider, term1_use_autoclose, term1_what_time, term1_programming_avs, term1_programming_server_nums, term1_programming_tips, term1_programming_invoice_num, term1_programming_purchasing_cards, term1_accept_debit, term1_pin_pad_type, term1_pin_pad_qty, term2_quantity, term2_type, term2_provider, term2_use_autoclose, term2_what_time, term2_programming_avs, term2_programming_server_nums, term2_programming_tips, term2_programming_invoice_num, term2_programming_purchasing_cards, term2_accept_debit, term2_pin_pad_type, term2_pin_pad_qty, owner1_percentage, owner1_fullname, owner1_title, owner1_address, owner1_city, owner1_state, owner1_zip, owner1_phone, owner1_fax, owner1_email, owner1_ssn, owner1_dob, owner2_percentage, owner2_fullname, owner2_title, owner2_address, owner2_city, owner2_state, owner2_zip, owner2_phone, owner2_fax, owner2_email, owner2_ssn, owner2_dob, referral1_business, referral1_owner_officer, referral1_phone, referral2_business, referral2_owner_officer, referral2_phone, referral3_business, referral3_owner_officer, referral3_phone, rep_contractor_name, fees_rate_discount, fees_rate_structure, fees_qualification_exemptions, fees_startup_application, fees_auth_transaction, fees_monthly_statement, fees_misc_annual_file, fees_startup_equipment, fees_auth_amex, fees_monthly_minimum, fees_misc_chargeback, fees_startup_expedite, fees_auth_aru_voice, fees_monthly_debit_access, fees_startup_reprogramming, fees_auth_wireless, fees_monthly_ebt, fees_startup_training, fees_monthly_gateway_access, fees_startup_wireless_activation, fees_monthly_wireless_access, fees_startup_tax, fees_startup_total, fees_pin_debit_auth, fees_ebt_discount, fees_pin_debit_discount, fees_ebt_auth, rep_discount_paid, rep_amex_discount_rate, rep_business_legitimate, rep_photo_included, rep_inventory_sufficient, rep_goods_delivered, rep_bus_open_operating, rep_visa_mc_decals_visible, rep_mail_tel_activity, created, modified, moto_inventory_owned, moto_outsourced_customer_service_field, moto_outsourced_shipment_field, moto_outsourced_returns_field, moto_sales_local, moto_sales_national, site_survey_signature, api, var_status, install_var_rs_document_guid, tickler_id, callback_url, guid, redirect_url) FROM stdin;
1	1	Lorem ip	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ip	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	t	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ip	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ip	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	1	1	1	1	1	1	1	1	1	t	t	t	t	t	t	t	t	t	t	t	t	Lorem ip	Lorem ip	Lorem ipsum dolor sit amet	t	t	t	t	Lorem ipsum dolor sit amet	t	t	t	t	Lorem ip	Lorem ip	Lorem ip	Lorem ip	Lorem ip	Lorem ip	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ip	Lorem ipsum dolor sit amet	Lorem ip	Lorem ip	1	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ip	Lorem ipsum dolor sit amet	t	t	t	t	t	Lorem ip	Lorem ipsum dolor sit amet	1	1	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ip	Lorem ipsum dolor sit amet	t	t	t	t	t	Lorem ip	Lorem ipsum dolor sit amet	1	1	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	1	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ipsum dolor 	Lorem ip	Lorem ipsum dolor 	Lorem ip	Lorem ip	Lorem ip	Lorem ip	Lorem ip	Lorem ip	Lorem ip	2012-08-15 12:04:23	2012-08-15 12:04:23	Lorem ip	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t	t	Lorem ipsum dolor sit amet	1	Lorem ip	Lorem ipsum dolor sit amet	\N	\N	\N	\N
\.


--
-- Data for Name: onlineapp_cobranded_application_aches; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_cobranded_application_aches (id, cobranded_application_id, description, auth_type, routing_number, account_number, bank_name, created, modified) FROM stdin;
\.


--
-- Data for Name: onlineapp_cobranded_application_values; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_cobranded_application_values (id, cobranded_application_id, template_field_id, name, value, created, modified) FROM stdin;
\.


--
-- Data for Name: onlineapp_cobranded_applications; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_cobranded_applications (id, user_id, template_id, uuid, created, modified, rightsignature_document_guid, status, rightsignature_install_document_guid, rightsignature_install_status) FROM stdin;
\.


--
-- Data for Name: onlineapp_cobrands; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_cobrands (id, partner_name, partner_name_short, logo_url, description, created, modified, response_url_type) FROM stdin;
\.


--
-- Data for Name: onlineapp_coversheets; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_coversheets (id, onlineapp_application_id, user_id, status, setup_existing_merchant, setup_banking, setup_statements, setup_drivers_license, setup_new_merchant, setup_business_license, setup_other, setup_field_other, setup_tier_select, setup_tier3, setup_tier4, setup_tier5_financials, setup_tier5_processing_statements, setup_tier5_bank_statements, setup_equipment_terminal, setup_equipment_gateway, setup_install, setup_starterkit, setup_equipment_payment, setup_lease_price, setup_lease_months, setup_debit_volume, setup_item_count, setup_referrer, setup_referrer_type, setup_referrer_pct, setup_reseller, setup_reseller_type, setup_reseller_pct, setup_notes, cp_encrypted_sn, cp_pinpad_ra_attached, cp_giftcards, cp_check_guarantee, cp_check_guarantee_info, cp_pos, cp_pos_contact, micros, micros_billing, gateway_option, gateway_package, gateway_gold_subpackage, gateway_epay, gateway_billing, moto_online_chd, moto_developer, moto_company, moto_gateway, moto_contact, moto_phone, moto_email, created, modified, cobranded_application_id) FROM stdin;
\.


--
-- Data for Name: onlineapp_email_timeline_subjects; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_email_timeline_subjects (id, subject) FROM stdin;
1	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: onlineapp_email_timelines; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_email_timelines (id, app_id, date, email_timeline_subject_id, recipient, cobranded_application_id) FROM stdin;
1	\N	2012-08-15 12:37:04	\N	Lorem ipsum dolor sit amet	\N
\.


--
-- Data for Name: onlineapp_epayments; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_epayments (id, pin, application_id, merchant_id, user_id, onlineapp_application_id, date_boarded, date_retrieved) FROM stdin;
1	1	1	00000000-0000-0000-0000-000000000001	1	1	2012-08-15 12:37:35	2012-08-15 12:37:35
\.


--
-- Data for Name: onlineapp_groups; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_groups (id, name, created, modified) FROM stdin;
\.


--
-- Data for Name: onlineapp_multipasses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_multipasses (id, merchant_id, device_number, username, pass, in_use, application_id, created, modified) FROM stdin;
\.


--
-- Data for Name: onlineapp_settings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_settings (key, value, description) FROM stdin;
Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
\.


--
-- Data for Name: onlineapp_template_fields; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_template_fields (id, name, description, rep_only, width, type, required, source, default_value, merge_field_name, "order", section_id, created, modified, encrypt) FROM stdin;
\.


--
-- Data for Name: onlineapp_template_pages; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_template_pages (id, name, description, rep_only, template_id, "order", created, modified) FROM stdin;
\.


--
-- Data for Name: onlineapp_template_sections; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_template_sections (id, name, description, rep_only, width, page_id, "order", created, modified) FROM stdin;
\.


--
-- Data for Name: onlineapp_templates; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_templates (id, name, logo_position, include_axia_logo, description, cobrand_id, created, modified, rightsignature_template_guid, rightsignature_install_template_guid, owner_equity_threshold) FROM stdin;
\.


--
-- Data for Name: onlineapp_users; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_users (id, email, password, group_id, created, modified, token, token_used, token_uses, firstname, lastname, extension, active, api_password, api_enabled, cobrand_id, template_id) FROM stdin;
\.


--
-- Data for Name: onlineapp_users_managers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_users_managers (id, user_id, manager_id) FROM stdin;
\.


--
-- Data for Name: onlineapp_users_onlineapp_cobrands; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_users_onlineapp_cobrands (id, user_id, cobrand_id) FROM stdin;
\.


--
-- Data for Name: onlineapp_users_onlineapp_templates; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.onlineapp_users_onlineapp_templates (id, user_id, template_id) FROM stdin;
\.


--
-- Data for Name: orderitem_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.orderitem_types (id, orderitem_type_id_old, orderitem_type_description) FROM stdin;
24614a81-bde0-49a5-a234-2a9dabdfcf66	1	New Order
4d165bc9-71bb-4ebc-98b5-d805589a7a74	2	Replacement
0d16d8b7-e155-45d3-891e-90383f9d5a3f	3	Refurbished
\.


--
-- Data for Name: orderitems; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.orderitems (id, order_id, equipment_type_id, equipment_item_description, quantity, equipment_item_true_price, equipment_item_rep_price, equipment_item_id, hardware_sn, hardware_replacement_for, item_tax, item_ship_cost, depricated_commission_month, item_date_ordered, orderitem_id, warranty, warranty_id, merchant_id, item_merchant_id, item_ship_type, shipping_type_item_id, item_ach_seq, merchant_ach_id, orderitem_type_id, type_id, commission_month, commission_year, equipment_item_partner_price) FROM stdin;
13571745-28e4-4d1e-bb62-4a6d7546e841	07afa03c-7a54-4732-8d21-2aab3abddc1b	1dd22698-efa4-43ee-80cc-5ca62e732708	Nurit 2085 Ref	1	248.75	259	338ac402-7e15-4d0d-b4aa-845d1257d326	79U034144816	\N	0	\N	3:2004	\N	\N	\N	\N	47cf6834-1ac0-4c50-b4c8-d851335897ab	47cf6834-1ac0-4c50-b4c8-d851335897ab	\N	\N	\N	\N	24614a81-bde0-49a5-a234-2a9dabdfcf66	909f75a3-1159-4196-909f-72935e17bb47	3	2004	\N
760ae5da-a785-454c-a5be-613086cf8a9d	07afa03c-7a54-4732-8d21-2aab3abddc1b	1dd22698-efa4-43ee-80cc-5ca62e732708	Micr Mini Ref	1	135	155	05f3aa5e-2623-487e-b38f-6c1924a4815f	a012tyd	\N	0	\N	3:2004	\N	\N	\N	\N	47cf6834-1ac0-4c50-b4c8-d851335897ab	47cf6834-1ac0-4c50-b4c8-d851335897ab	\N	\N	\N	\N	4d165bc9-71bb-4ebc-98b5-d805589a7a74	909f75a3-1159-4196-909f-72935e17bb47	3	2004	\N
26695264-22c6-4fc2-b2cc-62818d0971f7	07afa03c-7a54-4732-8d21-2aab3abddc1b	4f15efcf-4df8-47c8-b7b3-e1268f5e7923	Tax	1	16.7600000000000016	16.7600000000000016	\N	\N	\N	0	\N	3:2004	\N	\N	\N	\N	47cf6834-1ac0-4c50-b4c8-d851335897ab	47cf6834-1ac0-4c50-b4c8-d851335897ab	\N	\N	\N	\N	\N	\N	3	2004	\N
e570c50e-b367-4f4d-9dc8-f0ec010e2cb6	07afa03c-7a54-4732-8d21-2aab3abddc1b	4f15efcf-4df8-47c8-b7b3-e1268f5e7923	Shipping	1	25.0700000000000003	25.0700000000000003	\N	\N	\N	0	\N	3:2004	\N	\N	\N	\N	47cf6834-1ac0-4c50-b4c8-d851335897ab	47cf6834-1ac0-4c50-b4c8-d851335897ab	\N	\N	\N	\N	\N	\N	3	2004	\N
f570c50e-b367-4f4d-9dc8-f0ec010e2cb6	17afa03c-7a54-4732-8d21-2aab3abddc1b	00000000-0000-0000-0000-000000000002	Test description 1	1	\N	300	\N	\N	\N	40	16.879999999999999	12:2015	\N	\N	\N	\N	00000000-0000-0000-0000-000000000003	00000000-0000-0000-0000-000000000003	\N	\N	\N	66a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	12	2015	\N
00000000-0000-0000-0000-000000000006	27afa03c-7a54-4732-8d21-2aab3abddc1b	00000000-0000-0000-0000-000000000003	Test description 2	1	\N	100	\N	\N	\N	40	16.879999999999999	12:2015	\N	\N	\N	\N	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	\N	\N	\N	\N	\N	12	2015	\N
00000000-0000-0000-0000-000000000007	37afa03c-7a54-4732-8d21-2aab3abddc1b	00000000-0000-0000-0000-000000000003	Test description 3	1	\N	250	7d5e6da1-47bd-4f06-ace1-04be3a83f445	\N	\N	40	16.879999999999999	12:2015	\N	\N	\N	\N	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	\N	\N	\N	\N	\N	12	2015	\N
\.


--
-- Data for Name: orderitems_replacements; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.orderitems_replacements (id, orderitem_id, shipping_axia_to_merchant_id, shipping_axia_to_merchant_cost, shipping_merchant_to_vendor_id, shipping_vendor_to_axia_id, shipping_vendor_to_axia_cost, ra_num, tracking_num_old, date_shipped_to_vendor, date_arrived_from_vendor, amount_billed_to_merchant, tracking_num, orderitem_replacement_id, shipping_merchant_to_vendor_cos) FROM stdin;
e4e18377-f1b9-4461-9888-33d35f5c14cd	760ae5da-a785-454c-a5be-613086cf8a9d	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
ca3a2caa-f5fb-410f-804e-5c0be3e0e7f9	13571745-28e4-4d1e-bb62-4a6d7546e841	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.orders (id, order_id_old, status, user_id, user_id_old, date_ordered, date_paid, merchant_id, merchant_id_old, shipping_cost, tax, ship_to, tracking_number, notes, invoice_number, shipping_type_id, shipping_type_old, display_id, merchant_ach_id, ach_seq_number_old, depricated_commission_month, vendor_id, vendor_id_old, add_item_tax, commission_month_nocharge, commission_month, commission_year) FROM stdin;
07afa03c-7a54-4732-8d21-2aab3abddc1b	\N	PAID	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	2004-03-08	2004-08-24	47cf6834-1ac0-4c50-b4c8-d851335897ab	\N	25.0700	16.7600	Matt Sakauye	\N	\N	683097	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	83e96265-539b-41a1-a16d-cc08a64082e2	\N	\N	00000000-0000-0000-0000-000000000001	\N	0	0	\N	\N
17afa03c-7a54-4732-8d21-2aab3abddc1b	687	PAID	00000000-0000-0000-0000-000000000010	\N	2015-12-25	2015-12-25	00000000-0000-0000-0000-000000000003	\N	16.8800	\N	\N	\N	\N	\N	\N	3	\N	66a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	00000000-0000-0000-0000-000000000001	\N	0	0	\N	\N
27afa03c-7a54-4732-8d21-2aab3abddc1b	787	PAID	32165df7-6b97-4f86-9e6f-8638eb30cd9e	\N	2015-12-25	2015-12-25	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	\N	1.0000	\N	\N	\N	\N	\N	\N	3	\N	76a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	00000000-0000-0000-0000-000000000001	\N	0	0	\N	\N
37afa03c-7a54-4732-8d21-2aab3abddc1b	887	PAID	43265df7-6b97-4f86-9e6f-8638eb30cd9e	\N	2015-12-25	2015-12-25	5f4587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	16.8800	\N	\N	\N	\N	\N	\N	3	\N	96a6ba9a-2098-460f-97b3-ddbab6a6b17b	\N	\N	00000000-0000-0000-0000-000000000001	\N	0	0	\N	\N
\.


--
-- Data for Name: organizations; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.organizations (id, name, active) FROM stdin;
8d08c7ed-c61a-4311-b088-706d6d8c052c	Test Org 1	t
\.


--
-- Data for Name: original_acquirers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.original_acquirers (id, acquirer) FROM stdin;
55cc6d7e-a12c-4ffb-9ea9-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-ae74-4bd5-830e-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-b70c-464e-ae1c-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-be78-4c49-94d6-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-c5e4-4529-88f8-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-d7dc-4aa7-bb73-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-e330-482a-a113-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-ea9c-4d30-99e7-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-f1a4-407b-a131-181f34627ad4	Lorem ipsum dolor sit amet
55cc6d7e-f910-475f-b6d1-181f34627ad4	Lorem ipsum dolor sit amet
56e057b3-e860-4d89-be16-319134627ad4	Axia (before 10/1/14)
\.


--
-- Data for Name: partners; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.partners (id, partner_id_old, partner_name, active) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	1
\.


--
-- Data for Name: payment_fusion_rep_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.payment_fusion_rep_costs (id, user_compensation_profile_id, rep_monthly_cost, rep_per_item, standard_device_cost, vp2pe_device_cost, pfcc_device_cost, vp2pe_pfcc_device_cost) FROM stdin;
\.


--
-- Data for Name: payment_fusions; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.payment_fusions (id, merchant_id, generic_product_mid, account_fee, rate, monthly_total, per_item_fee, other_features, standard_num_devices, standard_device_fee, vp2pe_num_devices, vp2pe_device_fee, pfcc_num_devices, pfcc_device_fee, vp2pe_pfcc_num_devices, vp2pe_pfcc_device_fee, is_hw_as_srvc) FROM stdin;
\.


--
-- Data for Name: payment_fusions_product_features; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.payment_fusions_product_features (id, payment_fusion_id, product_feature_id) FROM stdin;
\.


--
-- Data for Name: pci_billing_histories; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.pci_billing_histories (id, pci_billing_type_id, saq_merchant_id, billing_date, date_change, operation) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	2012-08-15	2012-08-15	Lorem ipsum dolor sit a
\.


--
-- Data for Name: pci_billing_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.pci_billing_types (id, name) FROM stdin;
00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: pci_billings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.pci_billings (id, pci_billing_type_id, saq_merchant_id, billing_date) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	2012-08-15
\.


--
-- Data for Name: pci_compliance_date_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.pci_compliance_date_types (id, id_old, name) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: pci_compliance_status_logs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.pci_compliance_status_logs (id, pci_compliance_date_type_id, pci_compliance_date_type_id_old, saq_merchant_id, saq_merchant_id_old, date_complete, date_change, operation) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	2012-08-15	2012-08-15 12:31:00	Lorem ipsum dolor sit a
\.


--
-- Data for Name: pci_compliances; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.pci_compliances (id, pci_compliance_date_type_id, pci_compliance_date_type_id_old, saq_merchant_id, saq_merchant_id_old, date_complete) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	2012-08-15
\.


--
-- Data for Name: permission_caches; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.permission_caches (id, user_id, permission_id, permission_name) FROM stdin;
534c019a-1214-4ebf-85ac-3b0634627ad4	00ccf87a-4564-4b95-96e5-e90df32c46c1	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet
534c019a-79fc-4fb9-8b7d-3b0634627ad4	a7c2365f-4fcf-42bf-a997-fa3faa3b0eda	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet
534c019a-ca10-4223-8c29-3b0634627ad4	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet
534c019a-18f8-4d7d-b2cb-3b0634627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000005	Lorem ipsum dolor sit amet
534c019a-677c-49a7-9545-3b0634627ad4	6133b88d-5c48-4703-8fb3-68a7fe275ea3	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet
534c019a-b59c-47ea-bc59-3b0634627ad4	113114ae-da7b-4970-94bd-fa0c0f25c786	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet
534c019a-0290-4e81-86fe-3b0634627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet
534c019a-4fe8-481e-83cc-3b0634627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000002	Lorem ipsum dolor sit amet
534c019a-b834-4b9b-9a99-3b0634627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000003	Lorem ipsum dolor sit amet
534c019a-071c-41aa-b167-3b0634627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000004	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: permission_constraints; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.permission_constraints (id, user_id, permission_id) FROM stdin;
53171c5c-db38-4cec-a72d-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001
53171c5c-156c-4379-a986-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000002
53171c5c-3b50-4d47-a97f-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000003
53171c5c-5e78-4ed5-a8a2-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000004
53171c5c-813c-479f-9e71-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000005
53171c5c-a338-4522-a86d-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000006
53171c5c-c4d0-46e7-b51f-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000007
53171c5c-e6cc-4461-ad51-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000008
53171c5c-0990-43c1-b6b8-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000009
53171c5c-2bf0-4925-ac1d-579534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000010
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.permissions (id, name, description) FROM stdin;
53171c7c-9cb4-47a4-930a-57c434627ad4	permission-1	Lorem ipsum dolor sit amet
53171c7c-d6e8-4dbe-a92c-57c434627ad4	permission-2	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: permissions_roles; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.permissions_roles (id, role_id, permission_id) FROM stdin;
55842fec-b1c0-413a-8446-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001
55842fec-bf6c-4277-b7b2-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000002
55842fec-c804-4f43-a734-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000003
55842fec-d038-45cb-bbb0-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000004
55842fec-d808-482e-ade2-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000005
55842fec-dfd8-4342-ac45-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000006
55842fec-e744-4d46-80ec-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000007
55842fec-f040-478a-a5a6-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000008
55842fec-f874-4c28-a076-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000009
55842fec-00a8-46b3-801f-15e534627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000010
\.


--
-- Data for Name: posts; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.posts (id, title, body, published_month, published_year, published_fulldate) FROM stdin;
00000000-0000-0000-0000-000000000001	Man land on Mars	Sadly, he found nothing	8	2014	2012-08-15
00000000-0000-0000-0000-000000000002	Man return to Earth	He was empty handed	3	2015	2013-03-28
00000000-0000-0000-0000-000000000003	Matt Damon reaction	Nice try!	4	2015	2013-04-01
\.


--
-- Data for Name: pricing_matrices; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.pricing_matrices (id, matrix_id_old, user_id, user_id_old, user_type_id, user_type_old, matrix_profit_perc, matrix_new_monthly_volume, matrix_new_monthly_profit, matrix_view_conjunction, matrix_total_volume, matrix_total_accounts, matrix_total_profit, matrix_draw, matrix_new_accounts_min, matrix_new_accounts_max) FROM stdin;
\.


--
-- Data for Name: product_categories; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.product_categories (id, category_name) FROM stdin;
5bbcdde1-a174-43cc-925e-313134627ad4	Uncategorized Product
5bbcdde1-b5a0-4913-adbe-313134627ad4	ACH
5bbcdde1-8f98-433f-bb79-313134627ad4	Credit
5bbcdde1-5cac-487a-b201-313134627ad4	Debit/EBT
5bbcdde1-2448-4650-bfec-313134627ad4	Gateway
\.


--
-- Data for Name: product_features; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.product_features (id, products_services_type_id, feature_name) FROM stdin;
791b9e07-5d6c-4198-a8d7-0ce26f50e379	\N	VP2PE
5818d29f-d5d0-4828-9fdb-6cf234627ad4	\N	Starter
5818d29f-bf44-4588-b0e8-6cf234627ad4	\N	Corral
5818d29f-2ed8-4767-b27f-6cf234627ad4	\N	Corral Premium
\.


--
-- Data for Name: product_groups; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.product_groups (id, name) FROM stdin;
8d4f646c-6635-4e88-a2cf-31a984b5b2a2	Integrated and Partner Sales
4839a9b6-1299-4b8d-9540-399c99ab4a19	Merchant Sales
c0293488-57c4-4f29-9259-b2d66ab6edf1	Corral
\.


--
-- Data for Name: product_settings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.product_settings (id, merchant_id, products_services_type_id, product_feature_id, rate, monthly_fee, monthly_total, per_item_fee, gral_fee, gral_fee_multiplier, generic_product_mid) FROM stdin;
\.


--
-- Data for Name: products_and_services; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.products_and_services (id, merchant_id, merchant_id_old, products_services_type_id, products_services_type_id_old) FROM stdin;
5e1fc228-3952-4080-a5b3-f8f27e864da7	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	4541619740000068	952e7099-42b7-48d0-909b-6cb09d4d6706	BC
7772cd77-1021-4747-9f17-c6dd6cf68915	00000000-0000-0000-0000-000000000003	4541619740000224	72a445f3-3937-4078-8631-1f569d6a30ed	DBT
03d8db1a-11dc-4ee2-aa4c-a917caae3bec	00000000-0000-0000-0000-000000000003	3948000031000556	952e7099-42b7-48d0-909b-6cb09d4d6706	BC
44444444-4444-4444-4444-444444444444	00000000-0000-0000-0000-000000000003	\N	ad536ee9-1ecc-48ca-8461-3e4525c4727b	\N
cc495550-3f9b-41a1-9b0e-3459b7f4baf7	00000000-0000-0000-0000-000000000005	3948000031000557	952e7099-42b7-48d0-909b-6cb09d4d6706	BC
78811c32-d229-4560-a383-ed91c64bb4fa	cc3b2547-ea98-410d-ac22-1e8f733e8a79	3948000031000640	952e7099-42b7-48d0-909b-6cb09d4d6706	BC
00000000-0000-0000-0000-000000000007	00000000-0000-0000-0000-000000000004	4541619740000068	72a445f3-3937-4078-8631-1f569d6a30ed	DBT
00000000-0000-0000-0000-000000000008	00000000-0000-0000-0000-000000000004	4541619740000068	a293737b-34c5-4caf-82dd-40d41bb9e0e1	DISC
00000000-0000-0000-0000-000000000009	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	4541619740000068	e6d8f040-9963-4539-ab75-3e19f679de16	\N
\.


--
-- Data for Name: products_services_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.products_services_types (id, products_services_type_id_old, products_services_description, products_services_rppp, is_active, class_identifier, custom_labels, is_legacy, product_category_id) FROM stdin;
566f5f47-bac0-41f8-ac68-23f534627ad4	\N	American Express Discount	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
566f5f47-59c4-456a-9e1b-23f534627ad4	\N	Discover Discount	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
4ae44a5f-2daf-4bfc-8c47-ec2e2a3fbefe	AF	Annual Fee	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
0582831b-4aaf-404e-8d92-b6aaf89ac3f3	DISC	Discover	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
952e7099-42b7-48d0-909b-6cb09d4d6706	AMEX	American Express	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-fb1c-4e6b-bbea-1a3f34627ad4		American Express ESA	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-2220-4a58-b8d8-1a3f34627ad4		Credit Monthly	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-5288-4832-82b3-1a3f34627ad4		American Express Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-e1c0-44d4-ad38-1a3f34627ad4		American Express Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-78c8-4cce-a13f-1a3f34627ad4		American Express Non Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
69aa50fb-e11b-4239-985c-6bf05729fbba		American Express Flat Rate	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
13c449c9-00d7-4287-bfcc-342fac9a19e5		American Express Settled Items	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
cd7cc087-2c57-4875-8080-acba14bd5560		American Express Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-7d70-4622-ad25-1a3f34627ad4		Discover Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-ff60-40ee-b345-1a3f34627ad4		Discover Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-89e8-4a16-8d7d-1a3f34627ad4		Discover Non Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-3044-47e4-be9b-1a3f34627ad4		Visa	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
94ecbcac-1e2b-4ae1-892b-0d9e9ee68763		Discover Flat Rate	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
566f5f47-5b44-4096-8d66-23f534627ad4		Discover Settled Items	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
38d6c5b3-b6ca-4be8-9998-8b78976f0767		Discover Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
28b4aa2e-2559-4eee-b29a-b25808e16a10		Discover Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
ca336477-3c6a-417b-9594-7f452c2a266d		Discover Non Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-07e8-4715-820c-1a3f34627ad4		MasterCard Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-907c-4ba0-88b7-1a3f34627ad4		MasterCard Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-10dc-41e8-a72b-1a3f34627ad4		MasterCard Non Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-8fac-46fa-b824-1a3f34627ad4		MasterCard	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
3d4f7842-1cb9-4bad-b9fd-415b1d0eee30		MasterCard Discount	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
8e4c2407-8d65-4e5b-a485-0cb4106c8de9		MasterCard Flat Rate	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
cba097b8-6af9-4835-8422-2fee99e6b15a		MasterCard Settled Items	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
28dd4748-9699-41b9-9391-2524d3941018		MasterCard Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
728ff26a-8209-4cc4-86ae-e79a2df21e7b		MasterCard Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
fdafb816-4a0c-4e81-84df-96d794041372		MasterCard Non Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-90d0-4fa4-9613-1a3f34627ad4		Visa Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-0ed8-48c1-9161-1a3f34627ad4		Visa Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-9fe0-4420-830c-1a3f34627ad4		Visa Non Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
ec5b6684-6f5c-4881-b26c-5a8233bde091		Visa Discount	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
8e635b00-8866-4b08-a5c9-631980246991		Visa Flat Rate	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
2e298d95-345b-49e9-963e-5a95fa663355		Visa Settled Items	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
e6d8f040-9963-4539-ab75-3e19f679de16		Visa Sales	t	t	\N	\N	f	5bbcdde1-8f98-433f-bb79-313134627ad4
c907b3b8-2cf5-475b-898c-53ed1b0adaf6		Visa Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
624250b2-55ae-4423-b2e9-95c837943115		Visa Non Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
551038c2-08b8-47b5-aa38-1a3f34627ad4	\N	Discover T&E	t	f	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
62d2eb5d-e3b8-44e9-b1ac-d770289d7f46	\N	American Express Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
e5324035-1074-4a40-90f4-5cfc7c4fd10e	\N	American Express Non Dial Sales	t	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
5801936c-4498-498d-b0bc-398534627ad4	\N	American Express Discount (Converted Merchants)	\N	t	\N	\N	\N	5bbcdde1-8f98-433f-bb79-313134627ad4
72a445f3-3937-4078-8631-1f569d6a30ed	DBT	Debit Sales	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
7ea0907d-709e-4245-ab75-4a14b7c6e4c6	EBT	EBT	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-b298-4429-8bdd-1a3f34627ad4		Debit Monthly	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-aa5c-4ff9-92ea-1a3f34627ad4		Debit Authorizations	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-2f08-41ef-a283-1a3f34627ad4		Debit Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-af04-4663-b072-1a3f34627ad4		Debit Non Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
12093cfa-fecd-4f17-894f-2f3430008bb0		Debit Discount	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
d3448d6a-0248-4293-8766-4170259a9b55		Debit Flat Rate	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
53d72302-64f9-4311-a7d7-4303f86d01a5		Debit Settled Items	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
5c7f7e42-7eb0-44aa-90d2-b4eb8ed20973		Debit Dial Sales	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
34599067-71df-415d-b757-1280031bfc5f		Debit Non Dial Sales	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-2b7c-4330-982d-1a3f34627ad4		EBT Monthly	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-aa4c-4191-bc3b-1a3f34627ad4		EBT Authorizations	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-2d04-4c2b-9ce6-1a3f34627ad4		EBT Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
551038c2-b7f0-483b-a30e-1a3f34627ad4		EBT Non Dial Authorizations	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
5da88a37-c3fa-4ee6-a69d-91ff643b3b54		EBT Discount	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
4fff2b76-6619-46f9-b5ff-501bba60dfc5		EBT Flat Rate	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
3973f55d-484d-465b-9bf7-39ef555dabca		EBT Settled Items	t	f	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
f615203f-b266-4147-9d53-039480371607		EBT Sales	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
826f8c96-6f94-47fe-8f4c-1f67406bb07d		EBT Dial Sales	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
9bb75a3b-0a4d-4b1f-b75c-2392c52cc362		EBT Non Dial Sales	t	t	\N	\N	\N	5bbcdde1-5cac-487a-b201-313134627ad4
e8fa66a0-790f-4710-b7de-ef79be75a1c7	ACHP	ACH	t	t	pst_1	\N	\N	5bbcdde1-b5a0-4913-adbe-313134627ad4
551038c4-3370-47c3-bb4c-1a3f34627ad4	\N	ACH - Web Based	t	t	pst_2	\N	\N	5bbcdde1-b5a0-4913-adbe-313134627ad4
c468bf92-036c-4757-9609-d0dab4df1a3a	GW2	Gateway 2	t	t	\N	\N	\N	5bbcdde1-2448-4650-bfec-313134627ad4
f446b74f-ae19-4505-82c7-67cf93fcfe3d	GW1	Gateway 1	t	t	pst_4	\N	\N	5bbcdde1-2448-4650-bfec-313134627ad4
47b411c9-fdf3-4fc6-9c13-ba4e776c94da	WP	WebPASS	t	t	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
186e3343-686f-4766-be9e-eb41c1b9511b		Credit Authorizations	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
551038c2-798c-4f58-8096-1a3f34627ad4		Check Guarantee - Gross Profit	t	t	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
452d4a67-0a2a-4893-a854-47f14e58f53c	BC	Bankcard	t	f	\N	\N	t	5bbcdde1-a174-43cc-925e-313134627ad4
2c820913-c21a-4e0d-960b-9a70b156186d	VC	Virtual Check	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
750900ed-c15d-4184-8f34-c45a98b0ff2c	TE	T&E	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
ad536ee9-1ecc-48ca-8461-3e4525c4727b	GW	USA ePay	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
274c36b8-69b7-4f7f-adbf-cbab551d247f	HZ	Hooza	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
95f37ad6-bb94-45d3-9dc7-27b8f33a3359	AUTH	Authorize.net	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
b2e47785-924a-4b9e-b82c-d5a475c6a8e6	TG	TGate	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
551038c2-32ec-4a0a-b7ab-1a3f34627ad4	\N	Shop in Your Currency	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
551038c2-bb80-40a4-9c9e-1a3f34627ad4	\N	Pay in Your Currency	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
1aaca384-57a0-4d4c-aefc-4865f146c3e7	VCWEB	Web Based ACH	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
551038c4-8d80-42bb-b16d-1a3f34627ad4	\N	PYC	t	t	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
551038c2-10f8-4e7c-bbeb-1a3f34627ad4	\N	Gross Profit	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
551038c2-a47c-4c17-8812-1a3f34627ad4	\N	ATM	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
551038c4-87ec-48b9-bd22-1a3f34627ad4	\N	MCP	t	f	\N	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
762137fb-a8ef-457f-83b1-6591a1fd7596	CG	Check Guarantee	t	t	pst_3	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
ada3799b-1671-4239-b79b-4cabe2de2bbc	GIFT	Gift & Loyalty	t	t	pst_5	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
5806a480-cdd8-4199-8d6f-319b34627ad4	\N	Corral License Fee	t	t	p_set	a:3:{s:11:"monthly_fee";s:11:"License Fee";s:8:"gral_fee";s:27:"Per Additional Provider Fee";s:19:"gral_fee_multiplier";s:20:"Additional Providers";}	\N	5bbcdde1-a174-43cc-925e-313134627ad4
9db324ec-8365-4ae2-9b49-1e575113d5df	\N	Payment Fusion	t	t	pst_6	\N	\N	5bbcdde1-a174-43cc-925e-313134627ad4
\.


--
-- Data for Name: profit_projections; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.profit_projections (id, merchant_id, products_services_type_id, rep_gross_profit, rep_profit_amount, axia_profit_amount) FROM stdin;
\.


--
-- Data for Name: profitability_reports; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.profitability_reports (id, merchant_id, year, month, net_sales_vol, net_sales_item_count, gross_sales_vol, gross_sales_item_count, card_brand_cogs, processor_cogs, sponsor_bank_cogs, ithree_monthly_cogs, axia_net_income, axia_gross_profit, axia_net_profit, cost_of_goods_sold, total_income, total_residual_comp) FROM stdin;
\.


--
-- Data for Name: rate_structures; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.rate_structures (id, bet_table_id, structure_name, qual_exemptions) FROM stdin;
57dc5551-1234-4567-acd1-6c4434627ad4	5555555b-4444-3333-960d-20ba34627ad4	Pass Thru	Visa/MasterCard/Discover Interchange at Pass Thru
57dc5551-ae84-455e-acd1-6c4434627ad4	558ade3b-1da4-43a1-960d-20ba34627ad4	Pass Thru	Visa/MasterCard/Discover Interchange at Pass Thru
57dc5551-4950-4655-b646-6c4434627ad4	558ade3c-d180-423c-a50d-20ba34627ad4	Cost Plus	Visa/MasterCard/Discover Cost Plus .05%
c679b8df-cfe2-44be-80b2-a9c6c8e1d474	558ade3b-0a28-4c31-82a1-20ba34627ad4	Cost Plus	Visa/MasterCard/Discover Cost Plus .05%
6d70249b-e56c-4b27-9d9b-8241f94db83b	484f88f8-c3ac-48db-a967-af612c1772fd	Cost Plus	Visa/MasterCard/Discover Cost Plus .05%
6d70249b-e56c-4b27-9d9b-8241f94db83b	558ade3b-9990-4f05-bfed-20ba34627ad4	Cost Plus	American Express Cost Plus .05%
\.


--
-- Data for Name: referer_products_services_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.referer_products_services_xrefs (id, referer_id, ref_seq_number_old, products_services_type_id, products_services_type_id_old) FROM stdin;
\.


--
-- Data for Name: referers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.referers (id, ref_seq_number_old, ref_name, ref_ref_perc, active, ref_username, ref_password, ref_residuals, ref_commissions, is_referer) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	0.1000	1	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t	t	f
\.


--
-- Data for Name: referers_bets; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.referers_bets (id, referers_bet_id_old, referer_id, ref_seq_number_old, bet_id, bet_code_old, pct) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	1.0000
\.


--
-- Data for Name: regions; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.regions (id, organization_id, name) FROM stdin;
ef3b25d3-b4e7-4137-8663-42fc83f7fc71	8d08c7ed-c61a-4311-b088-706d6d8c052c	Org 1 - Region 1
\.


--
-- Data for Name: rep_cost_structures; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.rep_cost_structures (id, user_id, user_id_old, debit_monthly_fee, debit_per_item_fee, gift_statement_fee, gift_magstripe_item_fee, gift_magstripe_loyalty_fee, gift_chipcard_item_fee, gift_chipcard_loyalty_fee, gift_chipcard_onerate_fee, cg_volume, vc_web_based_rate, vc_web_based_pi, vc_monthly_fee, vc_gateway_fee, ach_merchant_based, ach_file_fee, ach_eft_ccd_w, ach_eft_ccd_nw, ach_eft_ppd_nw, ach_eft_ppd_w, ach_eft_rck, ach_reject_fee, ach_statement_fee, ach_secure_gateway_eft, ebt_per_item_fee, ebt_monthly_fee, main_statement_fee, main_vrt_term_gwy_fee, vc_web_based_monthly_fee, vc_web_based_gateway_fee, te_transaction_fee, af_annual_fee, multiple, auth_monthly_fee, auth_per_item_fee, debit_rate_rep_cost_pct, ach_rate, gw_rate, gw_per_item, debit_per_item_fee_tsys, debit_rate_rep_cost_pct_tsys, debit_per_item_fee_tsys_new, debit_rate_rep_cost_pct_tsys_new, discover_statement_fee, debit_per_item_fee_directconnect, debit_rate_rep_cost_pct_directconnect, debit_per_item_fee_sagepti, debit_rate_rep_cost_pct_sagepti, tg_rate, tg_per_item, wp_rate, wp_per_item, tg_statement_fee) FROM stdin;
dccd9e7a-f803-4f60-bd40-b7aa2e630d4a	58b96fab-15a2-4c16-b5bf-ddec9c7dcf2d	\N	\N	0.0800	10.0000	0.1000	0.1000	0.1000	0.1000	0.1000	1000.0000	0.1000	0.1000	10.0000	15.0000	0.1000	10.0000	0.10	\N	0.1000	0.1000	0.1000	10.0000	10.0000	0.1000	\N	\N	4.7500	5.0000	10.0000	0.1000	0.1000	20.0000	0.0100	10.0000	0.1000	0.0890	\N	0.1000	0.1000	0.1000	0.1000	0.1000	0.1000	10.0000	0.1000	0.0000	0.0900	0.0000	0.1000	0.1000	0.1000	0.1000	10.0000
\.


--
-- Data for Name: rep_monthly_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.rep_monthly_costs (id, user_compensation_profile_id, bet_network_id, credit_cost, debit_cost, ebt_cost) FROM stdin;
557b1019-7024-4dd5-b7d7-1f8734627ad4	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	0	0	0
\.


--
-- Data for Name: rep_partner_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.rep_partner_xrefs (id, user_id, user_id_old, partner_id, partner_id_old, mgr1_id, mgr1_id_old, mgr2_id, mgr2_id_old, profit_pct, multiple, mgr1_profit_pct, mgr2_profit_pct) FROM stdin;
\.


--
-- Data for Name: rep_product_profit_pcts; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.rep_product_profit_pcts (id, user_id, user_id_old, products_services_type_id, products_services_type_id_old, pct, multiple, pct_gross, do_not_display) FROM stdin;
c88e1da7-d185-42c8-bc11-22d724448387	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	d207d63a-9613-4ace-9374-897f750f3fba	\N	50.0000	0.0000	80.0000	f
00fe3845-8236-4c96-97af-590c8d4ba171	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	6ab0549b-a370-40fb-b362-469eb91f3aec	\N	50.0000	0.0000	80.0000	f
e957e396-522a-4354-ad3e-dd1a5f5d707b	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	30c80709-3102-435c-8659-23143f65c232	\N	50.0000	0.0000	80.0000	f
d02ada55-567c-4602-b1f0-26636d474c2a	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	30910281-5800-4f3f-b415-53482addefc9	\N	0.0000	0.0000	0.0000	f
f9c4ae60-7752-4f62-a4af-29381e38a74f	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	897b9357-2054-4b54-a75f-0f7f86459a3f	\N	50.0000	0.0000	80.0000	f
ea387d1b-bb09-4ef8-9352-b3f5c42c937f	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	e3213fc0-2deb-4530-bb0d-85f96fae0be7	\N	5.0000	0.0000	100.0000	f
8c8321da-4f5a-46a6-9f82-8249ca6b771b	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	50.0000	0.0000	80.0000	f
c60559d4-04a2-457b-a225-e97829721667	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	5f749de2-5e52-423b-8ac2-e36c751204d6	\N	50.0000	0.0000	80.0000	f
86e6c603-1eaf-41c1-94f4-99343a70e639	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	78e35a98-7ebd-4395-b0c1-7fc39aeba709	\N	50.0000	0.0000	80.0000	f
281ac262-6e9b-473c-97bd-fadc21eb3750	18788e0a-58b7-42e6-a222-563abfb2ceb9	\N	99a18cde-ba28-44d0-9f6f-cf5be2bc7158	\N	50.0000	0.0000	80.0000	f
\.


--
-- Data for Name: rep_product_settings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.rep_product_settings (id, user_compensation_profile_id, products_services_type_id, rep_monthly_cost, rep_per_item, provider_device_cost) FROM stdin;
66c73cb4-7da5-4624-a516-ce80d6073096	5570e7fe-5a64-4a93-8724-337a34627ad4	5806a480-cdd8-4199-8d6f-319b34627ad4	10.000	0.500	25.000
\.


--
-- Data for Name: residual_parameter_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_parameter_types (id, name, "order", type, value_type, is_multiple) FROM stdin;
00000000-0000-0000-0000-000000000001	Rep %	1	0	2	0
75adbe8c-7a0c-4ad3-92aa-faa3b234a003	Rep Multiple	2	0	2	1
00000000-0000-0000-0000-000000000003	Mgr %	3	1	2	0
00000000-0000-0000-0000-000000000004	Partner Rep %	5	2	2	0
00000000-0000-0000-0000-000000000005	Partner Rep Multiple	6	2	2	1
3254ae37-0a0a-411c-8c26-dc5fa8244088	Mgr Multiple	4	1	2	1
\.


--
-- Data for Name: residual_parameters; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_parameters (id, residual_parameter_type_id, products_services_type_id, associated_user_id, type, value, is_multiple, created, modified, tier, user_compensation_profile_id) FROM stdin;
53734791-0b08-44fe-b5e1-685134627ad4	00000000-0000-0000-0000-000000000001	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	1	0	2014-05-14 12:38:09	2014-05-14 12:38:09	1	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-db3c-40da-a676-685134627ad4	00000000-0000-0000-0000-000000000002	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	1	1	2014-05-14 12:38:09	2014-05-14 12:38:09	1	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-7a9c-47bd-9de2-685134627ad4	00000000-0000-0000-0000-000000000001	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	2	0	2014-05-14 12:38:09	2014-05-14 12:38:09	2	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-1614-471a-853a-685134627ad4	00000000-0000-0000-0000-000000000002	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	2	1	2014-05-14 12:38:09	2014-05-14 12:38:09	2	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-b060-47cb-be40-685134627ad4	00000000-0000-0000-0000-000000000001	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	3	0	2014-05-14 12:38:09	2014-05-14 12:38:09	3	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-4e30-430f-a33e-685134627ad4	00000000-0000-0000-0000-000000000002	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	3	1	2014-05-14 12:38:09	2014-05-14 12:38:09	3	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-e9a8-4de5-92db-685134627ad4	00000000-0000-0000-0000-000000000001	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	4	0	2014-05-14 12:38:09	2014-05-14 12:38:09	4	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-864c-4273-9704-685134627ad4	00000000-0000-0000-0000-000000000002	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	4	1	2014-05-14 12:38:09	2014-05-14 12:38:09	4	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-20fc-44d5-b8ab-685134627ad4	00000000-0000-0000-0000-000000000001	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	5	0	2014-05-14 12:38:09	2014-05-14 12:38:09	0	5570e7fe-5a64-4a93-8724-337a34627ad4
53734791-bb48-4f04-827b-685134627ad4	00000000-0000-0000-0000-000000000002	91c919e7-bff5-4f68-a03b-1721377b0057	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	5	1	2014-05-14 12:38:09	2014-05-14 12:38:09	0	5570e7fe-5a64-4a93-8724-337a34627ad4
35dee39b-4836-4123-85a4-db79973627b9	00000000-0000-0000-0000-000000000001	e6d8f040-9963-4539-ab75-3e19f679de16	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	5	0	2014-05-14 12:38:09	2014-05-14 12:38:09	0	6570e7dc-a4fc-444c-8929-337a34627ad4
\.


--
-- Data for Name: residual_pricings; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_pricings (id, merchant_id, merchant_id_old, products_services_type_id, products_services_type_id_old, user_id, user_id_old, r_network, r_month, r_year, r_rate_pct, r_per_item_fee, r_statement_fee, m_rate_pct, m_per_item_fee, m_statement_fee, refer_profit_pct, ref_seq_number_old, referer_id, m_hidden_per_item_fee, m_hidden_per_item_fee_cross, m_eft_secure_flag, m_gc_plan, m_pos_partner_flag, bet_table_id, bet_code_old, bet_extra_pct, pct_volume, res_profit_pct, reseller_id, res_seq_number_old, m_micros_ip_flag, m_micros_dialup_flag, m_micros_per_item_fee, m_wireless_flag, m_wireless_terminals, r_usaepay_flag, r_epay_retail_flag, r_usaepay_gtwy_cost, r_usaepay_gtwy_add_cost, m_tgate_flag, m_petro_flag, ref_p_type, ref_p_value, res_p_type, res_p_value, r_risk_assessment, ref_p_pct, res_p_pct) FROM stdin;
\.


--
-- Data for Name: residual_product_controls; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_product_controls (id, user_id, product_service_type_id, do_no_display, tier_product, enabled_for_rep, rep_params_gross_profit_tsys, rep_params_gross_profit_sage, rep_params_gross_profit_dc, manager1_params_gross_profit_tsys, manager1_params_gross_profit_sage, manager1_params_gross_profit_dc, manager2_params_gross_profit_tsys, manager2_params_gross_profit_sage, manager2_params_gross_profit_dc, override_rep_percentage, override_multiple, override_manager1, override_manager2) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	t	t	t	1	1	1	1	1	1	1	1	1	1	1	1	1
00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000001	t	t	t	2	2	2	2	2	2	2	2	2	2	2	2	2
00000000-0000-0000-0000-000000000003	00000000-0000-0000-0000-000000000003	00000000-0000-0000-0000-000000000001	t	t	t	3	3	3	3	3	3	3	3	3	3	3	3	3
00000000-0000-0000-0000-000000000004	00000000-0000-0000-0000-000000000004	00000000-0000-0000-0000-000000000001	t	t	t	4	4	4	4	4	4	4	4	4	4	4	4	4
00000000-0000-0000-0000-000000000005	00000000-0000-0000-0000-000000000005	00000000-0000-0000-0000-000000000001	t	t	t	5	5	5	5	5	5	5	5	5	5	5	5	5
00000000-0000-0000-0000-000000000006	00000000-0000-0000-0000-000000000006	00000000-0000-0000-0000-000000000001	t	t	t	6	6	6	6	6	6	6	6	6	6	6	6	6
00000000-0000-0000-0000-000000000007	00000000-0000-0000-0000-000000000007	00000000-0000-0000-0000-000000000001	t	t	t	7	7	7	7	7	7	7	7	7	7	7	7	7
00000000-0000-0000-0000-000000000008	00000000-0000-0000-0000-000000000008	00000000-0000-0000-0000-000000000001	t	t	t	8	8	8	8	8	8	8	8	8	8	8	8	8
00000000-0000-0000-0000-000000000009	a7c2365f-4fcf-42bf-a997-fa3faa3b0eda	551038c2-e1c0-44d4-ad38-1a3f34627ad4	t	t	t	9	9	9	9	9	9	9	9	9	9	9	9	9
00000000-0000-0000-0000-000000000010	a7c2365f-4fcf-42bf-a997-fa3faa3b0eda	00000000-0000-0000-0000-000000000099	t	t	t	10	10	10	10	10	10	10	10	10	10	10	10	10
\.


--
-- Data for Name: residual_reports; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_reports (id, merchant_id, merchant_id_old, products_services_type_id, products_services_type_id_old, user_id, user_id_old, r_network, r_month, r_year, r_rate_pct, r_per_item_fee, r_statement_fee, r_profit_pct, r_profit_amount, m_rate_pct, m_per_item_fee, refer_profit_pct, refer_profit_amount, ref_seq_number_old, referer_id, status, m_statement_fee, r_avg_ticket, r_items, r_volume, total_profit, manager_id, manager_id_old, manager_profit_pct, manager_profit_amount, bet_table_id, bet_code_old, bet_extra_pct, res_profit_pct, res_profit_amount, reseller_id, res_seq_number_old, manager_id_secondary, manager_id_secondary_old, manager_profit_pct_secondary, manager_profit_amount_secondary, ref_p_type, ref_p_value, res_p_type, res_p_value, ref_p_pct, res_p_pct, partner_id, partner_id_old, partner_exclude_volume, partner_profit_pct, partner_profit_amount, partner_rep_profit_pct, partner_rep_profit_amount, partner_rate, partner_per_item_fee, partner_statement_fee, partner_gross_profit, partner_pct_of_gross, referrer_rate, referrer_per_item_fee, referrer_statement_fee, referrer_gross_profit, referrer_pct_of_gross, reseller_rate, reseller_per_item_fee, reseller_statement_fee, reseller_gross_profit, reseller_pct_of_gross, merchant_state, rep_gross_profit, rep_pct_of_gross, partner_rep_rate, partner_rep_per_item_fee, partner_rep_statement_fee, partner_rep_gross_profit, partner_rep_pct_of_gross, manager_rate, manager_per_item_fee, manager_statement_fee, manager_gross_profit, manager2_rate, manager2_per_item_fee, manager2_statement_fee, manager2_gross_profit, manager2_pct_of_gross, manager_pct_of_gross, profit_pct, referer_rate, referer_per_item_fee, referer_statement_fee, referer_gross_profit, referer_pct_of_gross, referer_profit_pct, referer_profit_amount, reseller_profit_pct, reseller_profit_amount, original_m_rate_pct) FROM stdin;
e8e17192-9539-4aeb-8073-6d0896586fd5	6dcab640-1862-4436-99ed-658fb49c9d51	\N	897b9357-2054-4b54-a75f-0f7f86459a3f	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	aff88e90-7fc3-4609-ad04-fc7ae2868299	9.0000	2003.0000	1.4900	0.1800	4.7500	75.0000	-2.8500	1.6100	0.2000	0.0000	0.0000	\N	\N	A	0.0000	0.0000	0.0000	0.0000	-4.7500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
94c986fd-ba34-4292-ba6e-6be79f0e497d	bfe344c2-c909-4c88-92dd-2720798df0c7	\N	897b9357-2054-4b54-a75f-0f7f86459a3f	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	\N	5.0000	2003.0000	0.0000	0.0000	4.7500	75.0000	-2.8500	0.0000	0.0000	0.0000	0.0000	\N	\N	A	0.0000	0.0000	0.0000	0.0000	-4.7500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
b61b9ea8-1248-4e78-8699-891e90a1963f	04cb537d-7889-4942-9c1b-f5fa788a271c	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	\N	8.0000	2014.0000	0.0000	0.3500	5.0000	75.0000	-2.4000	0.0000	0.6000	\N	0.0000	\N	\N	A	0.0000	0.0000	4.0000	0.0000	-4.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
9e8b4fa7-ffc6-4048-a033-c2c7b1ce275b	075909df-174b-477f-88e3-b3c5e2fdd2aa	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	aff88e90-7fc3-4609-ad04-fc7ae2868299	2.0000	2015.0000	0.0000	0.3500	5.0000	75.0000	-2.1000	0.0000	0.6000	\N	0.0000	\N	\N	A	0.0000	0.0000	6.0000	0.0000	-3.5000	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	0.1500	-0.5250	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
0e07e5e6-4b74-440c-abad-4afc54fd8c7e	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	\N	7.0000	2015.0000	0.0000	0.3500	5.0000	75.0000	-0.7380	0.0000	0.4800	\N	0.0000	\N	\N	A	0.0000	0.0000	29.0000	0.0000	-1.2300	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	0.0500	-0.0615	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
2c4c6bb0-8f79-40a3-aded-962ecb400f93	00f23e2e-1520-4004-a814-ed0408a2278e	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	\N	9.0000	2015.0000	0.0000	0.3500	5.0000	75.0000	0.3300	0.0000	0.5000	\N	0.0000	\N	\N	A	0.0000	0.0000	37.0000	0.0000	0.5500	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
c25b3298-4509-450a-8943-b5caf48bf4a6	156dbd0a-e817-48f1-b26a-f1be4cc59098	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	\N	9.0000	2003.0000	0.0000	0.3500	5.0000	75.0000	2.5200	0.0000	0.4500	\N	0.0000	\N	\N	A	0.0000	0.0000	92.0000	0.0000	4.2000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
e7006d4a-e8d0-46d8-ac31-940a1256d1d0	00f23e2e-1520-4004-a814-ed0408a2278e	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	aff88e90-7fc3-4609-ad04-fc7ae2868299	2.0000	2014.0000	0.0000	0.3500	5.0000	75.0000	-0.3000	0.0000	0.5000	\N	0.0000	\N	\N	A	0.0000	0.0000	30.0000	0.0000	-0.5000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
40e924bb-761f-4088-bedf-fd0cda42c9ab	cfae6ab7-9604-4112-85b9-ee2d397d7d55	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	\N	10.0000	2015.0000	0.0000	0.3500	5.0000	\N	\N	0.0000	0.4500	\N	\N	\N	\N	A	0.0000	0.0000	0.0000	0.0000	-5.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
fc5e10af-aec9-4a73-b65b-4189f0d1799c	e43c9628-3331-4149-bcb3-97faa3467524	\N	350b814f-0711-4ee1-b146-345f9504bedc	\N	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	\N	11.0000	2015.0000	0.0000	0.3500	5.0000	\N	\N	0.0000	0.3500	\N	\N	\N	\N	A	0.0000	0.0000	0.0000	0.0000	-5.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000004	\N	566f5f47-bac0-41f8-ac68-23f534627ad4	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	\N	7.0000	2015.0000	0.0000	0.3500	5.0000	75.0000	-0.7380	0.0000	0.4800	\N	0.0000	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	A	0.0000	0.0000	29.0000	0.0000	-1.2300	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	0.0500	-0.0615	521df34a-0a90-4035-b90e-461534627ad4	\N	\N	\N	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	\N	\N	\N	\N	\N	\N	\N	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	f	0.1000	0.1200	1.0000	1.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	0.0000	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: residual_time_factors; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_time_factors (id, tier1_begin_month, tier1_end_month, tier2_begin_month, tier2_end_month, tier3_begin_month, tier3_end_month, tier4_begin_month, tier4_end_month, created, modified, user_compensation_profile_id) FROM stdin;
\.


--
-- Data for Name: residual_time_parameters; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_time_parameters (id, residual_parameter_type_id, products_services_type_id, associated_user_id, type, value, is_multiple, created, modified, tier, user_compensation_profile_id) FROM stdin;
5612b540-6d04-4108-a936-0d73c0a81cd6	00000000-0000-0000-0000-000000000001	e8fa66a0-790f-4710-b7de-ef79be75a1c7	\N	\N	5	0	2015-10-05 10:37:04	2015-10-06 14:24:02	4	\N
5612b540-8df0-410c-ba33-0d73c0a81cd6	00000000-0000-0000-0000-000000000002	e8fa66a0-790f-4710-b7de-ef79be75a1c7	\N	\N	5	1	2015-10-05 10:37:04	2015-10-06 14:24:02	4	\N
5612b540-a964-4de1-b4a6-0d73c0a81cd6	00000000-0000-0000-0000-000000000003	e8fa66a0-790f-4710-b7de-ef79be75a1c7	ffd135d7-1305-4723-9705-7790635d53c1	\N	5	0	2015-10-05 10:37:04	2015-10-06 14:24:02	4	\N
5612b540-a5fc-405f-a0b6-0d73c0a81cd6	00000000-0000-0000-0000-000000000001	551038c4-3370-47c3-bb4c-1a3f34627ad4	\N	\N	0	0	2015-10-05 10:37:04	2015-10-06 14:24:02	1	\N
5612b540-bf7c-47f5-bb31-0d73c0a81cd6	00000000-0000-0000-0000-000000000002	551038c4-3370-47c3-bb4c-1a3f34627ad4	\N	\N	0	1	2015-10-05 10:37:04	2015-10-06 14:24:02	1	\N
\.


--
-- Data for Name: residual_volume_tiers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.residual_volume_tiers (id, product_service_type_id, tier1_minimum_volume, tier1_minimum_gp, tier1_maximum_volume, tier1_maximum_gp, tier2_minimum_volume, tier2_minimum_gp, tier2_maximum_volume, tier2_maximum_gp, tier3_minimum_volume, tier3_minimum_gp, tier3_maximum_volume, tier3_maximum_gp, tier4_minimum_volume, tier4_minimum_gp, tier4_maximum_volume, tier4_maximum_gp, created, modified, user_compensation_profile_id) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.roles (id, parent_id, name, lft, rght) FROM stdin;
5536d1cf-8854-495d-b727-103134627ad4	5536d1cf-9f34-41c1-bbf1-103134627ad4	Admin I	20	21
5536d1cf-8f84-424f-bae9-103134627ad4	\N	Reseller	3	16
5536d1cf-2e7c-46de-baac-103134627ad4	\N	Installer	1	2
5536d1cf-6ef4-4664-be12-103134627ad4	5536d1cf-a728-48ac-ac23-103134627ad4	Rep	6	11
5536d1cf-db84-4f7a-8267-103134627ad4	5536d1cf-a728-48ac-ac23-103134627ad4	PartnerRep	12	13
5536d2bf-853c-4e51-8b0c-106434627ad4	\N	Admin IV	17	24
5536d1cf-113c-41c8-b283-103134627ad4	5536d2bf-853c-4e51-8b0c-106434627ad4	Admin III	18	23
5536d1cf-9f34-41c1-bbf1-103134627ad4	5536d1cf-113c-41c8-b283-103134627ad4	Admin II	19	22
5536d1cf-22f4-4347-a922-103134627ad4	5536d1cf-8f84-424f-bae9-103134627ad4	Referrer	4	15
5536d1cf-47b0-401c-b52b-103134627ad4	5536d1cf-c50c-47d7-a03b-103134627ad4	SM	8	9
5536d1cf-c50c-47d7-a03b-103134627ad4	5536d1cf-6ef4-4664-be12-103134627ad4	SM2	7	10
5536d1cf-a728-48ac-ac23-103134627ad4	5536d1cf-22f4-4347-a922-103134627ad4	Partner	5	14
\.


--
-- Data for Name: sales_goal_archives; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.sales_goal_archives (id, user_id, user_id_old, goal_month, goal_year, goal_accounts, goal_volume, goal_profits, actual_accounts, actual_volume, actual_profits, goal_statements, goal_calls, actual_statements, actual_calls) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	1	1	1	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000	1.0000
\.


--
-- Data for Name: sales_goals; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.sales_goals (id, user_id, user_id_old, goal_accounts, goal_volume, goal_profits, goal_statements, goal_calls, goal_month, created, modified) FROM stdin;
534bbe09-1ea4-4088-adaf-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	1.0000	1.0000	1.0000	1.0000	1.0000	1	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-dce0-4f6d-bb6a-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	2.0000	2.0000	2.0000	2.0000	2.0000	2	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-759c-48aa-9799-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	3.0000	3.0000	3.0000	3.0000	3.0000	3	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-0ad4-4ea1-94bf-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	4.0000	4.0000	4.0000	4.0000	4.0000	4	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-a4bc-4c19-8426-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	5.0000	5.0000	5.0000	5.0000	5.0000	5	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-360c-4e13-a29c-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	6.0000	6.0000	6.0000	6.0000	6.0000	6	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-c630-4fce-a898-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	7.0000	7.0000	7.0000	7.0000	7.0000	7	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-6c34-4016-b27a-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	8.0000	8.0000	8.0000	8.0000	8.0000	8	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-01d0-4f3c-8950-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	9.0000	9.0000	9.0000	9.0000	9.0000	9	2014-04-14 12:52:57	2014-04-14 12:52:57
534bbe09-976c-4e9b-ba6f-22d134627ad4	00000000-0000-0000-0000-000000000001	\N	10.0000	10.0000	10.0000	10.0000	10.0000	10	2014-04-14 12:52:57	2014-04-14 12:52:57
\.


--
-- Data for Name: saq_answers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_answers (id, id_old, saq_merchant_survey_xref_id, saq_merchant_survey_xref_id_old, saq_survey_question_xref_id, saq_survey_question_xref_id_old, answer, date) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	t	2012-08-15 12:36:47
\.


--
-- Data for Name: saq_control_scan_unboardeds; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_control_scan_unboardeds (id, id_old, merchant_id, merchant_id_old, date_unboarded) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	2012-08-15
\.


--
-- Data for Name: saq_control_scans; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_control_scans (id, merchant_id, merchant_id_old, saq_type, first_scan_date, first_questionnaire_date, scan_status, questionnaire_status, creation_date, dba, quarterly_scan_fee, sua, pci_compliance, offline_compliance, compliant_date, host_count, submids) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	Lo	2012-08-15	2012-08-15	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.	2012-08-15	Lorem ipsum dolor sit amet	1	2012-08-15 12:24:39	L	L	2012-08-15	1	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
\.


--
-- Data for Name: saq_merchant_pci_email_sents; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_merchant_pci_email_sents (id, id_old, saq_merchant_id, saq_merchant_id_old, saq_merchant_pci_email_id, saq_merchant_pci_email_id_old, date_sent) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	2012-08-15 12:04:37
\.


--
-- Data for Name: saq_merchant_pci_emails; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_merchant_pci_emails (id, id_old, priority, "interval", title, filename_prefix, visible) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	1	1	Lorem ipsum dolor sit amet	Lorem ipsum dolor sit amet	t
\.


--
-- Data for Name: saq_merchant_survey_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_merchant_survey_xrefs (id, id_old, saq_merchant_id, saq_merchant_id_old, saq_survey_id, saq_survey_id_old, saq_eligibility_survey_id, saq_eligibility_survey_id_old, saq_confirmation_survey_id, saq_confirmation_survey_id_old, datestart, datecomplete, ip, acknowledgement_name, acknowledgement_title, acknowledgement_company, resolution) FROM stdin;
\.


--
-- Data for Name: saq_merchants; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_merchants (id, id_old, merchant_id, merchant_id_old, merchant_name, merchant_email, password, email_sent, billing_date, next_billing_date) FROM stdin;
c053a959-5627-4635-b156-3ae213abf818	\N	e3c717f4-4238-4856-96f6-107c7ec9381a	\N	Mary F. Frese	creamnsugarinc@test.com	LMCKJ6fOMPc=	\N	\N	\N
\.


--
-- Data for Name: saq_prequalifications; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_prequalifications (id, id_old, saq_merchant_id, saq_merchant_id_old, result, date_completed, control_scan_code, control_scan_message) FROM stdin;
00000000-0000-0000-0000-000000000043	\N	00000000-0000-0000-0000-000000000002	\N	A	2011-01-04 23:24:03.500117	\N	\N
00000000-0000-0000-0000-000000000044	\N	00000000-0000-0000-0000-000000000002	\N	B	2011-01-04 23:27:42.369238	\N	\N
00000000-0000-0000-0000-000000000661	\N	00000000-0000-0000-0000-000000000003	\N	D	2011-02-17 13:58:43	0	The transaction was successful
00000000-0000-0000-0000-000000003380	\N	00000000-0000-0000-0000-000000000004	\N	B	2011-06-21 07:59:06	\N	\N
00000000-0000-0000-0000-000000000827	\N	00000000-0000-0000-0000-000000000001	\N	C	2011-02-24 14:25:12	0	The transaction was successful
\.


--
-- Data for Name: saq_questions; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_questions (id, id_old, question) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.
\.


--
-- Data for Name: saq_survey_question_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_survey_question_xrefs (id, id_old, saq_survey_id, saq_survey_id_old, saq_question_id, saq_question_id_old, priority) FROM stdin;
\.


--
-- Data for Name: saq_surveys; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.saq_surveys (id, id_old, name, saq_level, eligibility_survey_id, eligibility_survey_id_old, confirmation_survey_id, confirmation_survey_id_old) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	Lo	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N
\.


--
-- Data for Name: schema_migrations; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.schema_migrations (id, class, type, created) FROM stdin;
1	InitMigrations	Migrations	2015-03-11 23:33:19
2	ConvertVersionToClassNames	Migrations	2015-03-11 23:33:19
3	IncreaseClassNameLength	Migrations	2015-03-11 23:33:19
4	CreateSchema	app	2015-03-11 23:33:23
5	AchAuthorize	app	2015-03-11 23:33:28
6	BankcardsToBets	app	2015-03-11 23:33:30
7	CancellationFeeToCommissionReport	app	2015-03-11 23:34:36
8	DebitToDiscoverUserBetTable	app	2015-03-11 23:34:39
9	EbtToEquipmentType	app	2015-03-11 23:34:41
10	GatewayToGroup	app	2015-03-11 23:34:41
11	LastDepositReportToMerchantCardTypes	app	2015-03-11 23:34:48
12	MerchantChange	app	2015-03-11 23:35:15
13	MerchantNoteToNetwork	app	2015-03-11 23:35:37
14	NoteTypeToPartners	app	2015-03-11 23:35:50
15	PciBillingToProductsServicesType	app	2015-03-11 23:35:53
16	RefererProductsServicesXrefToResidualReport	app	2015-03-11 23:40:51
17	SalesGoalToSystemTransaction	app	2015-03-11 23:42:09
18	TgateToTransactionType	app	2015-03-11 23:42:17
19	UsaepayRepGtwyAddCostToUwVerifiedOption	app	2015-03-11 23:42:36
20	VendorToWebpass	app	2015-03-11 23:42:36
21	DeleteOrphanedRecords	app	2015-03-11 23:42:40
22	UuidUpdateAchsToCheckGuarantees	app	2015-03-11 23:43:40
23	UuidUpdatecommissionPricingsToCommissionReports	app	2015-03-12 16:00:35
24	UuidUpdateDebitAcquirersToGroups	app	2015-03-12 16:03:29
25	UuidUpdateLastDepositReportsToMerchantCardTypes	app	2015-03-12 16:04:03
26	UuidUpdateMerchantChanges	app	2015-03-12 16:13:17
27	UuidUpdateMerchantNotesToNetworks	app	2015-03-12 16:16:17
28	UuidUpdateNoteTypesToOrders	app	2015-03-12 16:16:54
29	UuidUpdatePartnersToRepProductProfitPcts	app	2015-03-12 16:17:11
30	UuidUpdateResidualPricingsToResidualReports	app	2015-03-12 18:08:20
31	UuidUpdateSalesGoalArchivesToShippingTransactions	app	2015-03-12 18:09:43
32	UuidUpdateSystemTransactions	app	2015-03-12 19:02:11
33	UuidUpdateTgatesToUsers	app	2015-03-12 19:04:24
34	UuidUpdateUwApprovalinfoMerchantXrefsToWebpasses	app	2015-03-12 19:05:09
35	AddingResidualProductsTable	app	2015-03-12 19:05:09
36	AddingResidualVolumeTiersTable	app	2015-03-12 19:05:12
37	CleaningOrphanedBetTables	app	2015-03-12 19:05:13
38	BetsTables	app	2015-03-12 19:05:13
39	FillingTheBetTablesTable	app	2015-03-12 19:05:15
40	FillingTheBetsTable	app	2015-03-12 19:05:17
41	AddingPositionFieldToNetworks	app	2015-03-12 19:05:17
42	GiftCardsTableAddNewFields	app	2015-03-12 19:05:18
43	InsertsForGiftCardProvidersTable	app	2015-03-12 19:05:18
44	AchesTableCreateField	app	2015-03-12 19:05:18
45	RBACInitialSchema	Rbac	2015-03-12 19:26:18
46	ChangingNameFieldForPermissions	Rbac	2015-03-12 19:26:18
47	CreateMerchantPricingsTable	app	2015-03-12 19:40:38
48	ChangesToPrepForMerchantPricingsMigration	app	2015-03-12 19:42:06
49	AddDefaultRoles	app	2015-03-12 19:42:06
50	AddingPermissionsUsingMigrations	app	2015-03-12 19:42:06
51	InsertNetworkIdIntoUserBetTablesTable	app	2015-03-12 19:45:07
52	UpdatesForMerchantsTableAndMerchantPricings	app	2015-03-12 19:47:46
53	AddBlockedToUsers	app	2015-03-12 19:50:10
54	InsertNetworkIdIntoDiscoverUserBetTablesTable	app	2015-03-12 19:57:22
55	UpdatingRolesAndPerms	app	2015-03-12 19:57:23
56	NewPermissions	app	2015-03-12 19:57:24
57	RepCostStructuresTableAddFieldsAndData	app	2015-03-12 19:57:24
58	SetProviderIdInGiftCardsTable	app	2015-03-12 19:57:24
59	AddAssociatedUsers	app	2015-03-12 19:57:24
60	NewPermissionsAssociatedUsers	app	2015-03-12 19:57:24
61	AddingCreatedModifiedToSalesGoals	app	2015-03-12 19:57:24
62	AddSalesGoalPermsToAdmin1	app	2015-03-12 19:57:24
63	AddingCommissionFees	app	2015-03-12 19:57:25
64	SetValueForCrGatewayFeesSeparateInMerchantPricings	app	2015-03-12 20:01:21
65	AddUserParameterTypes	app	2015-03-12 20:01:21
66	UserParametersTable	app	2015-03-12 20:01:21
67	AddingIsMultipleToUserParams	app	2015-03-12 20:01:21
68	RenameCardTypesFieldAndAddNewProducts	app	2015-03-12 20:13:41
69	ResidualGridOption1Tables	app	2015-03-12 20:13:41
70	ResidualVolumeTiersPermissions	app	2015-03-12 20:13:42
71	MerchantCancellationsHistories	app	2015-03-12 20:13:42
72	UpdatingProductServicesTypes	app	2015-03-12 20:13:43
73	MerchantAchesTableUpdates	app	2015-03-12 20:46:22
74	AppStatuses	app	2015-03-12 20:46:22
75	EquipmentCost	app	2015-03-12 20:46:22
76	EquipmentCostPermission	app	2015-03-12 20:46:22
77	AttritionRatioPermissions	app	2015-03-12 20:46:22
78	AttritionRatioTable	app	2015-03-12 20:46:22
79	AddResidualTimeParametersTable	app	2015-03-12 20:46:22
80	NewPermissionsResidualTimeFactors	app	2015-03-12 20:46:22
81	CreateUnderwritingVolumesTable	app	2015-03-12 20:47:57
82	VariousUnderwritingRelatedChanges	app	2015-03-12 20:47:57
83	RemoveOldRoles	app	2015-03-12 20:47:58
84	RemoveAssociatedPermissions	app	2015-03-12 20:47:58
85	NewPermissionsUserAssingRoles	app	2015-03-12 20:47:58
86	MerchantTradeReferencesMods	app	2015-03-12 20:51:01
87	AddBetNetworksTable	app	2015-03-12 20:51:01
88	NewBetsTable	app	2015-03-12 20:51:02
89	NewPermissionsBets	app	2015-03-12 20:51:02
90	RemakeCommisionFeesTable	app	2015-03-12 20:51:02
91	NewCommissionFeesPermissions	app	2015-03-12 20:51:02
92	UpdateEquipmentTypesTable	app	2015-03-12 20:58:24
93	CreateAndUpdateNetworksTables	app	2015-03-12 21:03:33
94	ChangeParametersTablesValueToAllowDecimalPercetages	app	2015-03-12 21:03:33
95	AddingFieldForMerchantTable	app	2015-03-12 21:04:26
96	AddingFieldToMerchantPricings	app	2015-03-12 21:04:26
97	SplitVisaAndMcVolumes	app	2015-03-12 21:04:26
98	PricingArchiveAndAssociatedTables	app	2015-03-12 21:04:26
99	UserRiskAssessmentTable	app	2015-03-12 21:04:26
100	RedistributeLegacyArchiveData	app	2015-03-12 21:04:26
101	UserCompensationProfileTable	app	2015-03-12 21:04:26
102	AddCompensationProfileForeingKeys	app	2015-03-12 21:04:26
103	AddUserCompensationProfileId	app	2015-03-12 21:04:26
104	AddMoreBetAndNetworksData	app	2015-03-12 21:09:57
105	MigrateReferersToUsers	app	2015-03-13 07:43:57
106	MigratePartnersAsUsers	app	2015-03-13 07:44:55
107	AddManagerFieldsToMerchants	app	2015-03-13 07:45:32
110	MultipassAndApiEnhancements	app	2014-06-20 15:34:16
111	ApiLogs	app	2014-06-20 15:34:18
112	CobrandedOnlineappCreateObjects	app	2014-06-20 15:34:18
113	OnlineappTemplateFieldsAddNewEncryptColumn	app	2014-06-20 15:34:18
114	OnlineappCobrandedApplicationAchesNewTable	app	2014-06-20 15:34:18
115	OnlineappEmailTimelineModifications	app	2014-06-20 15:34:18
116	OnlineappNewRightsignatureFields	app	2014-06-20 15:34:18
117	OnlineappCobrandedApplicationNewStatusField	app	2014-06-20 15:34:18
118	OnlineappNewRightsignatureInstallSheetFields	app	2014-06-20 15:34:18
119	OnlineappCoversheetModifications	app	2014-06-20 15:34:19
120	OnlineappCobrandsAddNewResponseUrlColumn	app	2014-08-19 12:06:46
121	AssignCobrandTemplateToUser	app	2014-11-25 10:11:52
122	OnlineappUpdateStateDefaultValsSwitchAbbrevAndFullname	app	2014-11-25 10:11:52
123	OnlineappNewUsersCobrandsTable	app	2014-11-25 10:11:52
124	OnlineappUsersDropCobrandIdField	app	2014-11-25 10:13:11
125	OnlineappNewUsersTemplatesTable	app	2014-11-25 10:13:11
126	OnlineappPopulateUsersCobrandsUsersTemplates	app	2014-11-25 10:13:14
127	OnlineappNewOwnerEquityThresholdField	app	2014-11-25 10:13:14
128	BuildIndexes	app	2015-02-18 12:07:47
129	MerchantOnlineappApplicationIdToCobrandedApplicationId	app	2015-02-18 13:34:14
130	LastDepositPermissions	app	2015-05-19 18:44:50
131	SetMerchantRejectUploadPermissions	app	2015-06-08 11:52:16
132	RbacPermsRejects	app	2015-07-02 11:59:31
133	AddPermsToMerchantRejectsEditRow	app	2015-07-10 12:29:38
134	AdditionalAmexRepCost	app	2015-07-30 09:53:22
135	AddMoreCardTypes	app	2015-07-30 09:53:23
136	ReplaceDataInBetTables	app	2015-07-30 09:53:28
137	RbacMerchantRejectLinesPermissions	app	2015-07-30 09:53:45
138	ProgrammaticUserProfilesSetup	app	2015-07-30 09:55:35
139	RejectsPermissionsDeleteCancel	app	2015-07-31 17:35:37
140	AddFieldToGateway1	app	2015-07-31 19:34:39
141	AddFieldToMerchantPricingAndArchive	app	2015-07-31 19:34:40
142	MigrateAchRepCosts	app	2015-07-31 19:34:40
143	WebBasedAchRepCost	app	2015-07-31 19:34:40
144	BetNetworkRepMonthlyCost	app	2015-07-31 19:34:40
145	RenameMerchantPricingArchiveField	app	2015-07-31 19:45:12
146	ReplaceDataInBetTables2	app	2015-07-31 20:22:54
147	UpdateBetTableForeingKeysInMerchantPricing	app	2015-07-31 20:22:57
148	AddAmexConvertedMerchantsProduct	app	2015-11-20 12:59:50
149	CorrectingForeignKeyName	app	2015-11-20 12:59:50
150	DropNotNullFromUsers	app	2015-11-20 12:59:50
151	DropUserIdFromCompensationProfileAssocTables	app	2015-11-20 12:59:51
152	LoggableBahavior	app	2015-11-23 10:58:51
154	MerchantChangeRequests	app	2015-11-23 17:25:15
155	MerchantNoteResolvedField	app	2015-12-01 13:37:15
156	GatewayCostStructure	app	2015-12-02 17:41:03
157	RemoveCostFromShippingTypeItems	app	2015-12-02 17:41:03
158	AddRoleIdToCompProfiles	app	2015-12-02 17:41:03
159	AddNewMerchantAchAppStatus	app	2015-12-09 11:06:19
160	DefaultUserCompensationProfilesMigration	app	2015-12-09 11:06:21
161	DefaultUserCompensationProfilesMigrationUserParameters	app	2015-12-09 11:16:28
162	DefaultUserCompensationProfilesMigrationCommissions	app	2015-12-09 11:16:29
163	DefaultUserCompensationProfilesMigrationAppStatuses	app	2015-12-09 11:16:34
164	DefaultUserCompensationProfilesMigrationResidualPercentages	app	2015-12-09 11:40:57
165	DefaultUserCompensationProfilesMigrationAchAndWebAchRepCosts	app	2015-12-09 11:40:58
166	DefaultUserCompensationProfilesMigrationMonthlyRepCosts	app	2015-12-09 11:41:03
167	DefaultUserCompensationProfilesMigrationAddlAmexRepCosts	app	2015-12-09 11:41:07
168	DefaultUserCompensationProfilesMigrationGatewayCostStructures	app	2015-12-09 11:41:12
169	DefaultUserCompensationProfilesMigrationBets	app	2015-12-09 11:42:39
170	ResidualReportsNewFields	app	2015-12-21 11:51:42
171	AddUserCompensationProfileIdToMerchantAchesTable	app	2015-12-28 17:05:57
172	MerchantNotesNewFields	app	2015-12-28 17:33:40
174	MerchantInformationChangesMigration	app	2015-12-29 18:11:50
175	MerchantInformationChangessMigration	app	2015-12-30 16:27:37
176	MerchantInformationChangesssMigration	app	2015-12-30 16:45:12
177	MerchantPArchiveAlterFields	app	2016-01-13 11:31:19
178	AssociateMerchantsToWebBasedAch	app	2016-01-13 11:31:20
179	UserActivity	app	2016-01-15 18:02:28
180	UserActivityPermissions	app	2016-01-15 18:02:29
181	UserActivity1	app	2016-01-18 15:57:46
182	UserActivity11	app	2016-01-18 15:58:55
183	UserActivity111	app	2016-01-19 10:01:29
184	OverrideParametersMigration	app	2016-01-21 13:28:34
185	OverrideParametersMigration1	app	2016-01-21 13:47:55
186	UpdateUserPasswordField	app	2016-01-26 11:05:28
187	MerchantNotesNewFieldss	app	2016-02-03 10:06:26
188	AddFieldsToCommissionReportsTable	app	2016-02-03 10:21:52
189	RenamingAmexVolumeConvertedMerchantsProd	app	2016-02-03 10:21:53
190	DiactivateUneededVolumeProducts	app	2016-02-03 10:21:53
191	AddFieldsToCommissionPricingsTable	app	2016-02-03 10:21:53
192	ChangeCommissionPricingsBetIdToBetTableId	app	2016-02-03 10:22:08
193	AddRepProductProfitPctFieldToCommissionPricingsTable	app	2016-02-03 10:22:08
194	MigrateRepProductProfitPctsMultipleToResidualParameters	app	2016-02-03 10:22:10
195	MigrateRepProductProfitPctsPctToResidualParameters	app	2016-02-03 10:22:17
196	MigrateUsersProductsRisks	app	2016-02-03 10:22:18
197	CommissionReportPermissions	app	2016-02-03 15:56:56
198	DiactivatingMoreOldProducts	app	2016-03-09 17:04:51
199	UpdatesForNetworksTable	app	2016-03-09 17:04:51
200	UpdatingBackEndNetworksTable	app	2016-03-09 17:04:51
201	UpdatingOriginalAcquirers	app	2016-03-09 17:04:51
202	UpdatingBetNetworks	app	2016-03-09 17:07:37
203	FixBetTableIdForMcBet6144	app	2016-03-09 17:07:37
204	FixingMerchantsForeignKeyAssociationWithVISANET	app	2016-03-21 18:10:21
205	AddingNewBetsToBetTables	app	2016-03-31 16:15:56
206	AddingNewMerchantAcquirers	app	2016-05-16 10:30:09
207	RenamingAcquirer	app	2016-05-16 10:30:09
208	RenamingOriginalAcquirers	app	2016-05-16 10:30:09
209	ReactivatingSomeBets	app	2016-05-16 10:30:09
210	MerchantDataAndProductsMigrateMd1	app	2016-05-16 10:31:31
211	MerchantDataAndProductsMigrateMd2	app	2016-05-16 10:31:31
212	MerchantDataAndProductsMigrateMd3	app	2016-05-16 10:31:32
213	MerchantDataAndProductsMigrateMd4	app	2016-05-16 10:31:32
214	MerchantDataAndProductsMigrateMd5	app	2016-05-16 10:31:32
215	MerchantDataAndProductsMigrateMd6	app	2016-05-16 10:31:32
216	MerchantDataAndProductsMigrateBetTablesData	app	2016-05-16 10:31:33
217	MerchantDataAndProductsAssignProducts	app	2016-05-16 10:31:33
218	AddFieldToGetway1s	app	2016-05-20 10:06:23
219	EncryptBankAccountsInAchesTable	app	2016-05-20 10:06:24
220	MigrateResidualPricings	app	2016-05-20 10:06:24
221	AddingFieldsToCommissionPricings	app	2016-06-02 14:53:37
222	ChangeYearAndMonthFieldToInteger	app	2016-06-02 14:53:38
223	FixMerchantMid	app	2016-10-18 15:37:43
224	AddFieldsToResidualReportsTable	app	2016-10-18 15:37:57
225	ChangeResidualReportsBetIdToBetTableId	app	2016-10-18 15:38:05
226	AddControlScanMerchantSyncResults	app	2016-10-18 15:38:08
227	AddFieldToLoggableLogsTable	app	2016-10-18 15:38:16
228	AddClassFieldForProductsServices	app	2016-10-18 15:38:29
229	AddFieldToMerchantPcis	app	2016-10-18 15:38:35
230	AddColumnToMerchAcquirers	app	2016-10-18 15:38:41
231	AddFieldsMerchantUwVolumes	app	2016-10-18 15:38:44
232	CreateRateStructures	app	2016-10-18 15:38:48
233	AddFieldToUwVolumes	app	2016-10-18 15:38:54
234	AddNewMerchantBin	app	2016-10-18 15:38:56
235	AddColMerchantPricings	app	2016-10-18 15:38:56
236	RemoveNotNullInfodocAndApprovalInfos	app	2016-10-18 15:38:56
237	AddCorralProductAndRemoveOld	app	2016-10-18 15:38:56
238	CreateProductsSettingsTable	app	2016-10-18 15:39:07
240	AddFieldProductsServicesTypes	app	2016-10-18 15:39:12
241	SetCustomFields	app	2016-10-18 15:39:12
242	CreateRepProductsSettings	app	2016-10-18 15:39:13
243	SetClassIdCorralAndPFusion	app	2016-10-18 15:47:37
244	CreateBrandsTable	app	2016-10-26 14:41:11
245	AddBrandIdToMerchantsTable	app	2016-10-26 14:41:23
246	AlterMerchantNotesNoteDateField	app	2016-12-05 15:24:32
247	UpdateMerchantUwFinalApprovers	app	2016-12-05 15:25:18
248	AddPriorityFieldToUwRequireds	app	2016-12-05 15:36:57
249	AddFieldMerchantAchAppStatus	app	2016-12-05 15:36:57
250	MigrateMoreMerchantChangesData	app	2016-12-05 15:37:34
251	SplitOrderItemsCommissionMonth	app	2016-12-05 15:44:20
252	AddingFieldsCommissionAndResidualTables	app	2016-12-08 16:24:24
253	SetAxiaDbPermissions	app	2017-01-03 13:12:00
254	CreateAddressesOwnerFkConstraint	app	2018-09-12 12:49:20
255	AddColumnLastDepostReportTable	app	2018-09-12 12:49:20
256	BetTableCorrectionsForI3	app	2018-09-12 12:49:20
257	MidUpdatesForGatewayOnlyMerchants	app	2018-09-12 12:49:20
258	CustomSortSequencesUniquenesCorrections	app	2018-09-12 12:49:20
259	CopyApprovalDateFromTimeEntryToUw	app	2018-09-12 12:49:20
260	CreatePaymentFusionTables	app	2018-09-12 12:49:20
261	RemovedOrphanedRecordsAssociatedWithUcpData	app	2018-09-12 12:49:20
262	SetPaymentFusionData	app	2018-09-12 12:49:20
263	CreateNewAchOnlyMerchants	app	2018-09-12 12:49:20
264	AddFieldToPaymentFusionProduct	app	2018-09-12 12:49:20
265	AddFieldToGateway1Table	app	2018-09-12 12:49:20
266	GroupsRegionsSubregionsForMerchants	app	2018-09-12 12:49:20
267	CreateProfitabilityReportTable	app	2018-09-12 12:49:20
268	UpdateMerchantsInstallDates	app	2018-10-09 09:57:05
269	AddProductCategories	app	2018-10-09 09:57:05
270	CreateBgProcessTrackingTables	app	2018-11-12 12:41:11
271	CreateProfitProjectionsTable	app	2019-01-18 08:49:36
272	AddOrgsAndRegions	app	2019-03-11 13:17:02
273	AssignOrgsAndRegions	app	2019-03-11 13:17:02
281	AddNewTimelineItem	app	2019-03-11 13:29:34
282	AddApiUserFields	app	2019-03-11 13:29:34
288	ReencryptMerchantsData	app	2019-03-15 16:02:52
289	ReencryptMerchantBanksData	app	2019-03-15 16:02:52
290	ReencryptAchesData	app	2019-03-15 16:02:52
291	ReencryptMerchantOwnersData	app	2019-03-15 16:02:52
292	ReencryptMerchantUwVolumesData	app	2019-03-15 16:02:52
293	ReencryptLoggableLogsContentData	app	2019-05-15 16:02:19
294	AddAggreementEndTimelineItem	app	2019-05-15 16:02:19
302	UpdateMerchantAchBillingOptionsTable	app	2019-05-15 16:59:45
303	CreateInvoiceItemsTable	app	2019-05-15 17:01:44
304	CreateNonTaxableReasonsTable	app	2019-05-31 12:01:35
305	CreateUserBankingFields	app	2019-05-31 12:20:13
306	SetMerchantAchesCompleteDate	app	2019-05-31 12:35:24
308	AddColumnToMerchantAchReasons	app	2019-05-31 13:43:51
309	UpdateAxiapaymentsRatesAndFees	app	2019-07-19 15:44:06
310	AddAccountingFieldsToMerchantAches	app	2019-07-19 15:44:06
311	CreateNewApiConfigurationsTable	app	2019-08-01 13:06:12
312	MerchantAchReasonsMods	app	2019-08-05 15:23:19
314	AddExternalForeignIdField	app	2019-08-26 14:06:26
315	Add2FAFieldsToUsersTable	app	2020-07-06 09:24:46
316	AddFieldToMerchantAches	app	2020-07-06 09:24:59
317	AddMasterDataTables	app	2020-07-06 09:25:04
318	MassUpdateDicountRates	app	2020-11-24 23:35:06
319	AddFieldsToMerchantPicTable	app	2020-11-24 23:35:07
320	UpdateMerchansAsBoardedInControlScan	app	2020-11-24 23:37:13
321	UpdateAxiamedAcquiringClientsAsControlscanBoarded	app	2020-11-24 23:37:58
322	UpdateMoreAxiamedAcquiringClientsAsControlscanBoarded	app	2020-11-24 23:37:59
323	CreateClientsTableAndFields	app	2020-11-24 23:38:00
324	AddClientIdDataAndAssignToMerchants	app	2020-11-24 23:40:07
325	MoveSalesforceData	app	2022-01-27 20:52:56
326	AddChargebackEmailToMerchantsTable	app	2022-01-27 20:54:18
327	DropNotNullConstraitSaqMerchantsTable	app	2022-01-27 20:54:58
328	BoardDMerchantsToSysnetTheNewControlscan	app	2022-01-27 20:56:58
329	CreateMMerchantMidTypesTable	app	2022-01-27 21:04:11
330	AddFailedLoginInCountFieldUsersTable	app	2022-02-14 17:10:52
\.


--
-- Data for Name: shipping_type_items; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.shipping_type_items (id, shipping_type_id, shipping_type_old, shipping_type_description) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: shipping_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.shipping_types (id, shipping_type_old, shipping_type_description) FROM stdin;
00000000-0000-0000-0000-000000000001	1	shipping-type-1
00000000-0000-0000-0000-000000000002	2	shipping-type-2
00000000-0000-0000-0000-000000000003	3	shipping-type-3
\.


--
-- Data for Name: sponsor_banks; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.sponsor_banks (id, bank_name) FROM stdin;
5396371f-4804-43f1-8d77-53c534627ad4	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: subregions; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.subregions (id, region_id, organization_id, name) FROM stdin;
9b26cfbf-4511-4c5b-a10c-59d99b97bbd2	ef3b25d3-b4e7-4137-8663-42fc83f7fc71	8d08c7ed-c61a-4311-b088-706d6d8c052c	Org 1 - Region 1 - Subregion 1
\.


--
-- Data for Name: system_transactions; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.system_transactions (id, system_transaction_id_old, transaction_type_id, transaction_type_old, user_id, user_id_old, merchant_id, merchant_id_old, session_id, client_address, system_transaction_date, system_transaction_time, merchant_note_id, merchant_note_id_old, merchant_change_id, change_id_old, merchant_ach_id, ach_seq_number_old, order_id, order_id_old, programming_id, programming_id_old, login_date) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	2015-02-11	12:34:02	00000000-0000-0000-0000-000000000001	\N	\N	\N	\N	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	2015-02-11 00:00:00
00000000-0000-0000-0000-000000000002	\N	00000000-0000-0000-0000-000000000001	\N	00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	2015-02-15	12:35:02	00000000-0000-0000-0000-000000000001	\N	\N	\N	\N	\N	00000000-0000-0000-0000-000000000001	\N	00000000-0000-0000-0000-000000000001	\N	2015-02-15 00:00:00
\.


--
-- Data for Name: tgates; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tgates (id, merchant_id, merchant_id_old, tg_rate, tg_per_item, tg_statement) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	1.0000	1.0000	1.0000
\.


--
-- Data for Name: tickler_availabilities; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_availabilities (id, name) FROM stdin;
\.


--
-- Data for Name: tickler_availabilities_leads; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_availabilities_leads (id, availability_id, lead_id) FROM stdin;
\.


--
-- Data for Name: tickler_comments; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_comments (id, parent_id, foreign_key, user_id, lft, rght, model, approved, is_spam, title, slug, body, author_name, author_url, author_email, language, comment_type, created, modified) FROM stdin;
\.


--
-- Data for Name: tickler_companies; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_companies (id, name, created, modified) FROM stdin;
\.


--
-- Data for Name: tickler_equipments; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_equipments (id, name, created, modified) FROM stdin;
\.


--
-- Data for Name: tickler_followups; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_followups (id, name, created, modified) FROM stdin;
\.


--
-- Data for Name: tickler_leads; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_leads (id, business_name, business_address, city, state_id, zip, phone, mobile, fax, decision_maker, decision_maker_title, other_contact, other_contact_title, email, website, company_id, equipment_id, referer_id, reseller_id, user_id, status_id, likes1, likes2, likes3, dislikes1, dislikes2, dislikes3, created, modified, call_date, meeting_date, sign_date, app_created, nlp_id, volume, lat, lng) FROM stdin;
\.


--
-- Data for Name: tickler_leads_logs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_leads_logs (id, action, lead_id, status_id, created) FROM stdin;
\.


--
-- Data for Name: tickler_loggable_logs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_loggable_logs (id, action, model, foreign_key, source_id, content, created) FROM stdin;
\.


--
-- Data for Name: tickler_referers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_referers (id, name, created, modified) FROM stdin;
\.


--
-- Data for Name: tickler_resellers; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_resellers (id, name, created, modified) FROM stdin;
\.


--
-- Data for Name: tickler_schema_migrations; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_schema_migrations (id, class, type, created) FROM stdin;
\.


--
-- Data for Name: tickler_states; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_states (id, name, state) FROM stdin;
\.


--
-- Data for Name: tickler_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.tickler_statuses (id, name, created, modified, "order") FROM stdin;
\.


--
-- Data for Name: timeline_entries; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.timeline_entries (id, merchant_id, merchant_id_old, timeline_item_id, timeline_item_old, timeline_date_completed, action_flag) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000004	\N	a2f754b7-dbbc-4249-bd2c-d129815ac4c7	\N	2015-05-15	t
00000000-0000-0000-0000-000000000002	00000000-0000-0000-0000-000000000004	\N	bf5b7135-f0c0-4ef2-a638-58e2b357bb66	\N	2015-05-16	t
00000000-0000-0000-0000-000000000003	00000000-0000-0000-0000-000000000004	\N	17b613f5-58c6-4dcc-b081-a060387d0108	\N	2015-05-20	f
00000000-0000-0000-0000-000000000004	00000000-0000-0000-0000-000000000004	\N	a9130700-0813-4a86-80cb-19796a054c5c	\N	2015-06-04	t
00000000-0000-0000-0000-000000000005	00000000-0000-0000-0000-000000000003	\N	a2f754b7-dbbc-4249-bd2c-d129815ac4c7	\N	2016-01-19	t
00000000-0000-0000-0000-000000000006	00000000-0000-0000-0000-000000000003	\N	bf5b7135-f0c0-4ef2-a638-58e2b357bb66	\N	2016-01-20	t
00000000-0000-0000-0000-000000000007	00000000-0000-0000-0000-000000000003	\N	17b613f5-58c6-4dcc-b081-a060387d0108	\N	2016-01-20	f
00000000-0000-0000-0000-000000000008	00000000-0000-0000-0000-000000000003	\N	572b98ae-cef7-45a7-bdb8-601866e6517f	\N	2016-01-22	t
00000000-0000-0000-0000-000000000009	00000000-0000-0000-0000-000000000003	\N	a9130700-0813-4a86-80cb-19796a054c5c	\N	2016-01-23	t
00000000-0000-0000-0000-000000000010	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	a2f754b7-dbbc-4249-bd2c-d129815ac4c7	\N	2016-04-19	t
00000000-0000-0000-0000-000000000011	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	bf5b7135-f0c0-4ef2-a638-58e2b357bb66	\N	2016-04-20	t
00000000-0000-0000-0000-000000000012	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	\N	17b613f5-58c6-4dcc-b081-a060387d0108	\N	2016-05-01	t
\.


--
-- Data for Name: timeline_items; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.timeline_items (id, timeline_item_old, timeline_item_description) FROM stdin;
04cca8b8-d686-41a2-9540-61eba8514ba3	OEQ	Order Equip
0a84d63f-6976-4764-97fb-236deaded207	KIT	Kit Sent
16490e3c-b6ee-46bb-9f1d-6853ec2d9f24	CNT	Contact Merch
17b613f5-58c6-4dcc-b081-a060387d0108	INS	Go-Live date
2ce06a07-4572-4616-900b-7944fac74fc7	FBL	File Built
2d34d29e-3171-4933-b23c-313b4be4c013	EQR	Equip Rec'd
320c49d0-631a-4fb2-a428-332099904692	DEC	Declined
3fb9e110-1110-4177-84f5-4acd79359960	TST	Tested
51c3b92a-d0d8-45a9-93ee-4045ed7d3c0d	TAC	Ts & Cs Sent
572b98ae-cef7-45a7-bdb8-601866e6517f	SIS	Rec'd Signed Install Sheet
611580af-0aea-426c-a859-3d1b9a3480b9	FLW	Follow up call
77ef5361-0f16-48f6-985c-45afa5df0044	KEY	Keyed
9ea45748-d05f-491f-9d5c-c77e2c66bae8	DAY	Days to Go-Live
9eff98d4-621e-4ed2-adfb-73eaacbcc38f	SGN	Signed
a2f754b7-dbbc-4249-bd2c-d129815ac4c7	SUB	Submitted
a9130700-0813-4a86-80cb-19796a054c5c	INC	Install Commissioned
bf5b7135-f0c0-4ef2-a638-58e2b357bb66	APP	Approved
e6ebcd50-60f6-4daf-a752-4cac0334fec3	DWN	Download
f14126a0-edf6-4e41-a6eb-2a88cb39c09b	FAX	Received
f8014e00-1acc-409e-8cc9-a9ac691b4cb4	MPD	Month Paid
bc8feb41-d006-4d07-8bc1-dafc30fafedc	\N	Expected to Go-Live
\.


--
-- Data for Name: transaction_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.transaction_types (id, transaction_type_old, transaction_type_description) FROM stdin;
efc19985-cb87-452f-890b-1878c867f973	LGN	User Login
6df61985-0b71-4493-a758-967d3d2f549c	CHG	Change Request
9e2a7eca-38f8-4177-b294-aeaf9ae30af1	NOTE	Merchant Note
7f37ce66-1865-4100-9ce0-4b0d316fe11c	ACH	ACH Entry
b5d908cb-9730-4778-93b8-c17b53e4450e	UPD	Record Updated
fab6bf5b-366c-4e37-9840-3f77fd58c6aa	ORD	Equipment Order
05956718-fffc-4d8f-b01a-b113f3a1f419	PROG	Programming Change
00000000-0000-0000-0000-000000000001	\N	Transaction Type 1
00000000-0000-0000-0000-000000000002	\N	Transaction Type 2
\.


--
-- Data for Name: usaepay_rep_gtwy_add_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.usaepay_rep_gtwy_add_costs (id, id_old, name, cost) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	1.0000
\.


--
-- Data for Name: usaepay_rep_gtwy_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.usaepay_rep_gtwy_costs (id, id_old, name, cost) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	1.0000
\.


--
-- Data for Name: user_bet_tables; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.user_bet_tables (id, bet_id, bet_code_old, user_id, user_id_old, network_id, network_old, rate, pi) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	\N	58b96fab-15a2-4c16-b5bf-ddec9c7dcf2d	\N	1f0811ba-755d-43c8-9e93-85ce4df481d1	\N	1.0000	1.0000
\.


--
-- Data for Name: user_compensation_profiles; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.user_compensation_profiles (id, user_id, partner_user_id, is_partner_rep, is_default, is_profile_option_1, is_profile_option_2, role_id) FROM stdin;
00000000-0000-0000-0000-000000000001	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	f	t	0	0	\N
88888888-5a64-4a93-8724-888888888888	113114ae-8888-8888-8888-fa0c0f25c786	\N	f	t	0	0	\N
5570e7fe-5a64-4a93-8724-337a34627ad4	00ccf87a-4564-4b95-96e5-e90df32c46c1	113114ae-8888-8888-8888-fa0c0f25c786	f	t	0	0	\N
6570e7dc-a4fc-444c-8929-337a34627ad4	32165df7-6b97-4f86-9e6f-8638eb30cd9e	\N	f	t	1	0	\N
5570e7dc-a4fc-444c-8929-337a34627ad4	00000000-0000-0000-0000-000000000010	\N	f	t	0	0	\N
7570e7dc-a4fc-444c-8929-337a34627ad4	43265df7-6b97-4f86-9e6f-8638eb30cd9e	43265df7-6b97-4f86-9e6f-8638eb30cd9e	t	t	0	0	\N
8570e7dc-a4fc-444c-8929-337a34627ad4	54365df7-6b97-4f86-9e6f-8638eb30cd9e	\N	f	t	0	0	\N
a8c5607c-cf2e-4b5b-a2f9-fda5a95a3950	19b2c1da-943e-4761-829a-4ca54008dd58	\N	f	t	1	1	\N
8bbe2d12-2975-4466-a309-fcf4e721d468	32165df7-6b97-4f86-9e6f-8638eb30cd9e	19b2c1da-943e-4761-829a-4ca54008dd58	t	f	1	1	\N
121affc3-76f2-4600-a817-0fc44dffef79	9e02c4d9-c2e5-4c9e-be9b-6f0c99e6c6a1	\N	f	t	1	1	5536d1cf-47b0-401c-b52b-103134627ad4
6da436f8-518e-406c-97dd-4afe229a485b	14f3324b-5f7c-4913-9a54-f934c4dbf408	\N	f	t	0	0	\N
7770e7dc-a4fc-444c-8929-337a34627ad4	00000000-0000-0000-0000-000000000014	\N	f	t	0	0	5536d1cf-c50c-47d7-a03b-103134627ad4
cb589710-92cd-4d15-84f6-6f67e858637f	0d2a4ddf-45fe-4f79-bf30-aacdaf4af9d7	\N	f	t	1	1	\N
36863033-d883-40d6-8a11-fda414a94ad4	66bc014d-40bf-406a-934d-c29179b9e5b8	\N	f	t	1	1	\N
\.


--
-- Data for Name: user_costs_archives; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.user_costs_archives (id, merchant_pricing_archive_id, merchant_id, user_id, cost_pct, per_item_cost, statement_cost, monthly_statement_cost, risk_assmnt_pct, risk_assmnt_per_item, attrition_reduction_pct, ref_p_type, ref_p_value, res_p_type, res_p_value, ref_p_pct, res_p_pct, res_profit_pct, refer_profit_pct, is_hidden) FROM stdin;
\.


--
-- Data for Name: user_parameter_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.user_parameter_types (id, name, "order", type, value_type) FROM stdin;
81dbd2e6-21a2-4958-bd85-815c70f7d90a	Do Not Display	1	0	0
7fe729ff-036a-47e5-adda-156ce727da0d	Tier Products	2	0	0
a8268c33-2e9f-4bc0-b482-82fdbcc0cd69	Enabled for Rep	3	0	0
d78ed6bf-739f-43b9-a262-dd343d3bcb5b	% of Gross Profit	4	1	1
f21e606e-e320-4f5a-b0ad-2842078eb0d8	Manual Override	5	2	1
\.


--
-- Data for Name: user_parameters; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.user_parameters (id, user_parameter_type_id, merchant_acquirer_id, products_services_type_id, associated_user_id, type, value, created, modified, is_multiple, user_compensation_profile_id) FROM stdin;
534bdba9-e8a8-4a75-9406-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	1	2014-04-14 14:59:21	2014-04-14 14:59:21	1	00000000-0000-0000-0000-000000000001
534bdba9-b42c-4833-aaf4-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	2	2014-04-14 14:59:21	2014-04-14 14:59:21	2	00000000-0000-0000-0000-000000000001
534bdba9-59cc-4687-b6db-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	3	2014-04-14 14:59:21	2014-04-14 14:59:21	3	00000000-0000-0000-0000-000000000001
534bdba9-fd14-478f-a635-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	4	2014-04-14 14:59:21	2014-04-14 14:59:21	4	00000000-0000-0000-0000-000000000001
534bdba9-a2b4-4222-aca8-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	5	2014-04-14 14:59:21	2014-04-14 14:59:21	5	00000000-0000-0000-0000-000000000001
534bdba9-4598-4c61-8a04-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	6	2014-04-14 14:59:21	2014-04-14 14:59:21	6	00000000-0000-0000-0000-000000000001
534bdba9-fa10-4f9d-a14d-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	7	2014-04-14 14:59:21	2014-04-14 14:59:21	7	00000000-0000-0000-0000-000000000001
534bdba9-a208-4915-a535-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	8	2014-04-14 14:59:21	2014-04-14 14:59:21	8	00000000-0000-0000-0000-000000000001
534bdba9-4488-42cd-86c7-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	9	2014-04-14 14:59:21	2014-04-14 14:59:21	9	00000000-0000-0000-0000-000000000001
534bdba9-e9c4-4b6a-89d8-2ce134627ad4	81dbd2e6-21a2-4958-bd85-815c70f7d90a	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	10	2014-04-14 14:59:21	2014-04-14 14:59:21	10	00000000-0000-0000-0000-000000000001
d361b2b3-596e-4dab-a3df-d7e15b843777	d78ed6bf-739f-43b9-a262-dd343d3bcb5b	192f8406-836d-4341-a1ee-d9d568cdb53d	e6d8f040-9963-4539-ab75-3e19f679de16	32165df7-6b97-4f86-9e6f-8638eb30cd9e	Lorem ipsum dolor sit amet	10	2014-04-14 14:59:21	2014-04-14 14:59:21	0	6570e7dc-a4fc-444c-8929-337a34627ad4
3a61e332-56dd-4512-aeef-9af30b4778cf	d78ed6bf-739f-43b9-a262-dd343d3bcb5b	71646a03-93e9-4275-9f15-5f79b4a64179	e6d8f040-9963-4539-ab75-3e19f679de16	32165df7-6b97-4f86-9e6f-8638eb30cd9e	Lorem ipsum dolor sit amet	12	2014-04-14 14:59:21	2014-04-14 14:59:21	0	8bbe2d12-2975-4466-a309-fcf4e721d468
\.


--
-- Data for Name: user_residual_options; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.user_residual_options (id, name) FROM stdin;
534be9d2-2f2c-42d2-9d86-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-8c24-40d1-bde0-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-d1ac-4899-a463-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-1d10-43af-a025-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-60a4-4fd6-8a59-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-a370-4860-b2d1-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-e6a0-469a-8326-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-2908-4c49-b99c-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-6bd4-4afe-9539-31ed34627ad4	Lorem ipsum dolor sit amet
534be9d2-af04-40af-9757-31ed34627ad4	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: user_types; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.user_types (id, user_type_old, user_type_description) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Sales Manager
534bbc68-8084-4fe4-a85f-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-cc4c-42b3-ba5d-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-1558-40fb-8bd2-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-5d9c-4e15-88ad-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-a57c-44d9-8baf-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-ed5c-429d-b364-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-353c-42fc-8f27-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-8168-481e-ab28-221534627ad4	\N	Lorem ipsum dolor sit amet
534bbc68-cad8-4ac4-955f-221534627ad4	\N	Lorem ipsum dolor sit amet
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.users (id, user_id_old, user_type_id, user_type_old, user_title, user_first_name, user_last_name, username, password_2, user_email, user_phone, user_fax, user_admin, parent_user_id, parent_user_id_old, user_commission, inactive_date, last_login_date, last_login_ip, entity_id, entity_old, active, initials, manager_percentage, date_started, split_commissions, bet_extra_pct, secondary_parent_user_id, secondary_parent_user_id_old, manager_percentage_secondary, discover_bet_extra_pct, password, user_residual_option_id, is_blocked, referer_id, womply_user_enabled, womply_active, access_token, api_password, api_last_request, bank_name, routing_number, account_number, secret, opt_out_2fa, wrong_log_in_count) FROM stdin;
00ccf87a-4564-4b95-96e5-e90df32c46c1	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		Mark	Weatherford	mweatherford	38ac1d88c48d147b19325bbfb6615f11	mweatherford@axia-eft.com	8089860964		\N	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	\N	\N	\N	\N	00000000-0000-0000-0000-000000000001	\N	1	MW	35.0000	2008-03-01	f	f	\N	\N	\N	f	$2y$10$DYhG93b0qyJfen3ieNT83e9cqkFv25jHy8W1O10idPqc9cj7GQS6O	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	00000000-0000-0000-0000-000000000001	\N		Bill	McAbee	bmcabee	ceb8447cc4ab78d2ec34cd9f11e4bed2	bmcabee@test.com			\N	5f36be3e-3211-4b40-94e4-a46c1081e7e3	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
00000000-0000-0000-0000-000000000004	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		Bill	McAbee inactive	inactive-bmcabee	078cf6a4f62531d8a3d1f389f10e9dff0e106351	bmcabee@test.com			\N	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	0	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
00000000-0000-0000-0000-000000000005	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		Bill	McAbee blocked	blocked-bmcabee	078cf6a4f62531d8a3d1f389f10e9dff0e106351	bmcabee@test.com			\N	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	t	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
113114ae-da7b-4970-94bd-fa0c0f25c786	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		Mc	Payne	mpayne	078cf6a4f62531d8a3d1f389f10e9dff0e106351	mpayne@test.com			\N	5f36be3e-3211-4b40-94e4-a46c1081e7e3	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
113114ae-7777-7777-7777-fa0c0f25c786	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		Mr	Installer	installer	078cf6a4f62531d8a3d1f389f10e9dff0e106351	installer@test.com			\N	\N	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
113114ae-8888-8888-8888-fa0c0f25c786	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		The	Best Partner Rep	best-partner	078cf6a4f62531d8a3d1f389f10e9dff0e106351	partner@test.com			\N	5f36be3e-3211-4b40-94e4-a46c1081e7e3	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
113114ae-9999-9999-9999-fa0c0f25c786	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		No	Profile Partner Rep	noprofile-partner	078cf6a4f62531d8a3d1f389f10e9dff0e106351	partner-no@test.com			\N	5f36be3e-3211-4b40-94e4-a46c1081e7e3	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
00000000-0000-0000-0000-000000000010	506	5536d1cf-6ef4-4664-be12-103134627ad4	\N	\N	Ben	Franklin	bfranklin	68ac1d88c48d147b19325bbfb6615f11	bfranklin@axia-eft.com	8089860967	\N	\N	\N	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	BF	35.0000	2008-03-01	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
54365df7-6b97-4f86-9e6f-8638eb30cd9e	806	5536d1cf-6ef4-4664-be12-103134627ad4	\N	\N	A	Partner	apartner	98ac1d88c48d147b19325bbfb6615f11	apartner@axia-eft.com	8089860970	\N	\N	\N	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	AP	35.0000	2008-03-01	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
32165df7-6b97-4f86-9e6f-8638eb30cd9e	606	5536d1cf-6ef4-4664-be12-103134627ad4	\N	\N	Slim	Pickins	spickins	78ac1d88c48d147b19325bbfb6615f11	spickins@axia-eft.com	8089860968	\N	\N	\N	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	SP	35.0000	2008-03-01	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
43265df7-6b97-4f86-9e6f-8638eb30cd9e	706	5536d1cf-6ef4-4664-be12-103134627ad4	\N	\N	Bob	Apple	bapple	88ac1d88c48d147b19325bbfb6615f11	bapple@axia-eft.com	8089860969	\N	\N	\N	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	BA	35.0000	2008-03-01	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
00000000-0000-0000-0000-000000000014	306	5536d1cf-6ef4-4664-be12-103134627ad4	\N	\N	John	Smith	jsmith	48ac1d88c48d147b19325bbfb6615f11	jsmith@axia-eft.com	8089860965	\N	\N	\N	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	JS	35.0000	2008-03-01	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
d2b7550c-d761-40b7-a769-ca1cf2ac9332	406	5536d1cf-6ef4-4664-be12-103134627ad4	\N	\N	Frank	Williams	fwilliams	58ac1d88c48d147b19325bbfb6615f11	fwilliams@axia-eft.com	8089860966	\N	\N	\N	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	FW	35.0000	2008-03-01	f	f	\N	\N	\N	f	078cf6a4f62531d8a3d1f389f10e9dff0e106351	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
548bce29-9c0d-42e0-b46c-179151ca54b4	\N	\N	\N	\N	Mr. Refer	Referington	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
1d58a4cd-664a-4c08-9b38-94ffc7e61afa	\N	\N	\N	\N	Mr. Resel	Resellington	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
a7c2365f-4fcf-42bf-a997-fa3faa3b0eda	\N	871e3266-4855-4b71-80f2-efeaa6892469	\N		Noel	Golondrina	ngolondrina	3e47b75000b0924b6c9ba5759a7cf15d	ngolondrina@axiapayments.com			\N	5f36be3e-3211-4b40-94e4-a46c1081e7e3	\N	\N	\N	\N	\N	4d15b70f-90ef-44be-9afa-1f3012065316	\N	1	NRG	25.0000	2011-09-21	f	f	\N	\N	\N	f	$2y$10$DYhG93b0qyJfen3ieNT83e9cqkFv25jHy8W1O10idPqc9cj7GQS6O	\N	f	\N	f	t	\N	\N	\N	\N	\N	\N	\N	f	0
\.


--
-- Data for Name: users_products_risks; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.users_products_risks (id, merchant_id, user_id, products_services_type_id, risk_assmnt_pct, risk_assmnt_per_item) FROM stdin;
557621a9-ef88-4905-b24d-2a8134627ad4	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	32165df7-6b97-4f86-9e6f-8638eb30cd9e	e8fa66a0-790f-4710-b7de-ef79be75a1c7	0.1	0.5
557621a9-ef88-4906-b22d-2a8134627bc1	3bc3ac07-fa2d-4ddc-a7e5-680035ec1040	02eb04ab-1f70-4f80-bc83-e16f9a86e764	e8fa66a0-790f-4710-b7de-ef79be75a1c7	0.1	0.5
557621a9-0b44-476d-a082-2a8134627ad4	4e3587be-aafb-48c4-9b6b-8dd26b8e94aa	02eb04ab-1f70-4f80-bc83-e16f9a86e764	e8fa66a0-790f-4710-b7de-ef79be75a1c7	0.1	0.5
\.


--
-- Data for Name: users_roles; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.users_roles (id, role_id, user_id) FROM stdin;
5637c65c-3ce4-4f32-9941-7da934627ad4	5536d1cf-6ef4-4664-be12-103134627ad4	f31d81ee-6dfd-4263-80ce-588ea6802c86
00000000-0000-0000-0000-000000000002	5536d1cf-6ef4-4664-be12-103134627ad4	00ccf87a-4564-4b95-96e5-e90df32c46c1
00000000-0000-0000-0000-000000000003	5536d1cf-c50c-47d7-a03b-103134627ad4	d2b7550c-d761-40b7-a769-ca1cf2ac9332
5637c65c-610c-4e68-849e-7da934627ad4	5536d1cf-22f4-4347-a922-103134627ad4	00000000-0000-0000-0000-000000000005
5637c65c-c484-4ffc-8a36-7da934627ad4	5536d1cf-2e7c-46de-baac-103134627ad4	113114ae-7777-7777-7777-fa0c0f25c786
5637c65c-64ec-4fc3-855b-7da934627ad4	5536d1cf-8854-495d-b727-103134627ad4	a7c2365f-4fcf-42bf-a997-fa3faa3b0eda
5637c65c-dcb4-4ba8-b673-7da934627ad4	5536d1cf-6ef4-4664-be12-103134627ad4	29f4096d-88c6-420f-b59b-1137b3bcb309
5637c65c-7164-4374-803a-7da934627ad4	5536d1cf-6ef4-4664-be12-103134627ad4	00000000-0000-0000-0000-000000000004
5637c65c-e0f8-4e22-bbcf-7da934627ad4	5536d1cf-a728-48ac-ac23-103134627ad4	55144783-77a0-43a6-8474-554534627ad4
5637c65c-553c-468b-906f-7da934627ad4	5536d1cf-47b0-401c-b52b-103134627ad4	003166ed-45ce-4b08-8aaf-e4bf2c4fb9b6
5637c65c-c084-4361-86cf-7da934627ad4	5536d1cf-6ef4-4664-be12-103134627ad4	6c87a79c-a112-443a-a872-9033e13bab97
00000000-0000-0000-0000-000000000012	5319fae1-8db0-4b58-9204-60ce34627ad4	113114ae-da7b-4970-94bd-fa0c0f25c786
00000000-0000-0000-0000-000000000013	5536d1cf-8854-495d-b727-103134627ad4	a7c2365f-4fcf-42bf-a997-fa3faa3b0eda
00000000-0000-0000-0000-000000000014	5536d1cf-db84-4f7a-8267-103134627ad4	113114ae-8888-8888-8888-fa0c0f25c786
00000000-0000-0000-0000-000000000015	5536d1cf-db84-4f7a-8267-103134627ad4	113114ae-9999-9999-9999-fa0c0f25c786
00000000-0000-0000-0000-000000000016	5536d1cf-db84-4f7a-8267-103134627ad4	32165df7-6b97-4f86-9e6f-8638eb30cd9e
00000000-0000-0000-0000-000000000017	5536d1cf-47b0-401c-b52b-103134627ad4	00000000-0000-0000-0000-000000000010
\.


--
-- Data for Name: uw_approvalinfo_merchant_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_approvalinfo_merchant_xrefs (id, merchant_id, merchant_id_old, uw_approvalinfo_id, approvalinfo_id_old, uw_verified_option_id, verified_option_id_old, notes) FROM stdin;
\.


--
-- Data for Name: uw_approvalinfos; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_approvalinfos (id, id_old, name, priority, verified_type) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	1	yn
\.


--
-- Data for Name: uw_infodoc_merchant_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_infodoc_merchant_xrefs (id, merchant_id, merchant_id_old, uw_infodoc_id, infodoc_id_old, uw_received_id, received_id_old, notes) FROM stdin;
\.


--
-- Data for Name: uw_infodocs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_infodocs (id, id_old, name, priority, required) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	1	t
\.


--
-- Data for Name: uw_receiveds; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_receiveds (id, id_old, name, priority) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ips	1
\.


--
-- Data for Name: uw_status_merchant_xrefs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_status_merchant_xrefs (id, merchant_id, merchant_id_old, uw_status_id, status_id_old, datetime, notes) FROM stdin;
\.


--
-- Data for Name: uw_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_statuses (id, id_old, name, priority) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	1
00000000-0000-0000-0000-000000000002	\N	Approved	2
00000000-0000-0000-0000-000000000003	\N	Received	3
\.


--
-- Data for Name: uw_verified_options; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.uw_verified_options (id, id_old, name, verified_type) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	yn
\.


--
-- Data for Name: vendors; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.vendors (id, vendor_id_old, vendor_description, rank) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	vendor-1	1
55cc6447-2418-4924-8f8b-13d834627ad4	\N	vendor-2	2
55cc6447-2d14-442b-95c0-13d834627ad4	\N	vendor-3	3
55cc6447-3548-4d42-b428-13d834627ad4	\N	vendor-4	4
55cc6447-3d7c-41d6-9044-13d834627ad4	\N	vendor-5	5
55cc6447-454c-4d24-8ca0-13d834627ad4	\N	vendor-6	6
55cc6447-4d1c-47ff-8cca-13d834627ad4	\N	vendor-7	7
55cc6447-5550-4eb0-bea4-13d834627ad4	\N	vendor-8	8
55cc6447-5d20-439b-b075-13d834627ad4	\N	vendor-9	9
55cc6447-6554-41b5-b0b5-13d834627ad4	\N	vendor-10	10
\.


--
-- Data for Name: virtual_check_webs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.virtual_check_webs (id, merchant_id, merchant_id_old, vcweb_mid, vcweb_web_based_rate, vcweb_web_based_pi, vcweb_monthly_fee, vcweb_gateway_fee) FROM stdin;
55cc6e15-f31c-40bd-a49b-18e934627ad4	00000000-0000-0000-0000-000000000001	Lorem ipsum dolor sit amet	Lorem ipsum dolor 	0.0000	0.0000	0.0000	0.0000
\.


--
-- Data for Name: virtual_checks; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.virtual_checks (id, merchant_id, merchant_id_old, vc_mid, vc_web_based_rate, vc_web_based_pi, vc_monthly_fee, vc_gateway_fee) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor 	1.0000	1.0000	1.0000	1.0000
\.


--
-- Data for Name: visa_bets; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.visa_bets (id, bet_code_old, bet_processing_rate, bet_processing_rate_2, bet_processing_rate_3, bet_per_item_fee, bet_per_item_fee_2, bet_per_item_fee_3, bet_business_rate, bet_business_rate_2, bet_per_item_fee_bus, bet_per_item_fee_bus_2, bet_extra_pct) FROM stdin;
\.


--
-- Data for Name: warranties; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.warranties (id, warranty_old, warranty_description, cost) FROM stdin;
00000000-0000-0000-0000-000000000001	\N	Lorem ipsum dolor sit amet	1.0000
\.


--
-- Data for Name: web_ach_rep_costs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.web_ach_rep_costs (id, user_compensation_profile_id, rep_rate_pct, rep_per_item, rep_monthly_cost) FROM stdin;
55bbcdd0-f998-4d4d-be06-1f3634627ad4	5509dd2d-bb24-47f4-ba44-0e200a0100c1	0	0.55	0
56142ac4-9908-4df1-9262-67e2c0a81cd6	55db8591-1bc8-4336-b478-130b34627ad4	2	2	2
56143469-a7dc-4c49-a600-67e3c0a81cd6	5612bc94-4864-4080-b2a9-114fc0a81cd6	10	10	10
557a0cdb-9bcc-44d0-8ec1-54f534627ad4	5570e7d1-6b3c-4644-9c1a-337a34627ad4	0	0.55	0
557a0cdb-a010-44f9-9412-54f534627ad4	5570e7d2-8f80-4374-bbf4-337a34627ad4	0	0.55	0
\.


--
-- Data for Name: webpasses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.webpasses (id, merchant_id, merchant_id_old, wp_rate, wp_per_item, wp_statement) FROM stdin;
00000000-0000-0000-0000-000000000001	00000000-0000-0000-0000-000000000001	\N	1.0000	1.0000	1.0000
\.


--
-- Data for Name: womply_actions; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.womply_actions (id, id_old, action) FROM stdin;
a1eed1e7-e2fc-4e0c-9177-66518070ea7a	1	CREATED
220a8b50-fcde-407e-8331-54896eda82cd	2	UPDATED
6d5faed3-a401-4827-995c-eb57ae26fb08	3	DELETED
89d6ea34-58ca-4c0d-9ad5-b619785e49f9	4	RECREATED
\.


--
-- Data for Name: womply_merchant_logs; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.womply_merchant_logs (id, id_old, merchant_id, merchant_id_old, womply_action_id, womply_action_id_old, details, created) FROM stdin;
\.


--
-- Data for Name: womply_statuses; Type: TABLE DATA; Schema: public; Owner: axia
--

COPY public.womply_statuses (id, id_old, status) FROM stdin;
7cb1eec8-7eb8-43d8-8e63-7f6a4a8d28a9	1	CALL
855dedd6-5f57-4f25-8607-c01bcfe3f848	2	DNC
6f0a9862-7165-484a-9391-03ea52d3d5f1	3	SOLD-ESS
\.


--
-- Name: onlineapp_apips_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_apips_id_seq', 1, true);


--
-- Name: onlineapp_applications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_applications_id_seq', 1, true);


--
-- Name: onlineapp_cobranded_application_aches_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_cobranded_application_aches_id_seq', 1, false);


--
-- Name: onlineapp_cobranded_application_values_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_cobranded_application_values_id_seq', 1, false);


--
-- Name: onlineapp_cobranded_applications_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_cobranded_applications_id_seq', 1, false);


--
-- Name: onlineapp_cobrands_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_cobrands_id_seq', 1, false);


--
-- Name: onlineapp_coversheets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_coversheets_id_seq', 1, false);


--
-- Name: onlineapp_email_timeline_subjects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_email_timeline_subjects_id_seq', 1, true);


--
-- Name: onlineapp_email_timelines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_email_timelines_id_seq', 1, true);


--
-- Name: onlineapp_epayments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_epayments_id_seq', 1, true);


--
-- Name: onlineapp_groups_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_groups_id_seq', 1, false);


--
-- Name: onlineapp_template_fields_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_template_fields_id_seq', 1, false);


--
-- Name: onlineapp_template_pages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_template_pages_id_seq', 1, false);


--
-- Name: onlineapp_template_sections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_template_sections_id_seq', 1, false);


--
-- Name: onlineapp_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_templates_id_seq', 1, false);


--
-- Name: onlineapp_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_users_id_seq', 1, true);


--
-- Name: onlineapp_users_onlineapp_cobrands_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_users_onlineapp_cobrands_id_seq', 1, false);


--
-- Name: onlineapp_users_onlineapp_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.onlineapp_users_onlineapp_templates_id_seq', 1, false);


--
-- Name: schema_migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.schema_migrations_id_seq', 330, true);


--
-- Name: tickler_loggable_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.tickler_loggable_logs_id_seq', 1, false);


--
-- Name: tickler_schema_migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: axia
--

SELECT pg_catalog.setval('public.tickler_schema_migrations_id_seq', 1, false);


--
-- Name: ach_providers ach_providers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.ach_providers
    ADD CONSTRAINT ach_providers_pkey PRIMARY KEY (id);


--
-- Name: ach_rep_costs ach_rep_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.ach_rep_costs
    ADD CONSTRAINT ach_rep_costs_pkey PRIMARY KEY (id);


--
-- Name: aches achs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.aches
    ADD CONSTRAINT achs_pkey PRIMARY KEY (id);


--
-- Name: addl_amex_rep_costs addl_amex_rep_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.addl_amex_rep_costs
    ADD CONSTRAINT addl_amex_rep_costs_pkey PRIMARY KEY (id);


--
-- Name: address_types address_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.address_types
    ADD CONSTRAINT address_types_pkey PRIMARY KEY (id);


--
-- Name: addresses addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_pkey PRIMARY KEY (id);


--
-- Name: adjustments adjustments_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.adjustments
    ADD CONSTRAINT adjustments_pkey PRIMARY KEY (id);


--
-- Name: admin_entity_views admin_entity_views_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.admin_entity_views
    ADD CONSTRAINT admin_entity_views_pkey PRIMARY KEY (id);


--
-- Name: amexes amexes_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.amexes
    ADD CONSTRAINT amexes_pkey PRIMARY KEY (id);


--
-- Name: api_configurations api_configurations_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.api_configurations
    ADD CONSTRAINT api_configurations_pkey PRIMARY KEY (id);


--
-- Name: app_statuses app_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.app_statuses
    ADD CONSTRAINT app_statuses_pkey PRIMARY KEY (id);


--
-- Name: articles articles_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (id);


--
-- Name: associated_external_records associated_external_records_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.associated_external_records
    ADD CONSTRAINT associated_external_records_pkey PRIMARY KEY (id);


--
-- Name: associated_users associated_users_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.associated_users
    ADD CONSTRAINT associated_users_pkey PRIMARY KEY (id);


--
-- Name: attrition_ratios attrition_ratios_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.attrition_ratios
    ADD CONSTRAINT attrition_ratios_pkey PRIMARY KEY (id);


--
-- Name: authorizes authorizes_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.authorizes
    ADD CONSTRAINT authorizes_pkey PRIMARY KEY (id);


--
-- Name: back_end_networks back_end_networks_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.back_end_networks
    ADD CONSTRAINT back_end_networks_pkey PRIMARY KEY (id);


--
-- Name: background_jobs background_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.background_jobs
    ADD CONSTRAINT background_jobs_pkey PRIMARY KEY (id);


--
-- Name: bankcards bankcards_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.bankcards
    ADD CONSTRAINT bankcards_pkey PRIMARY KEY (id);


--
-- Name: bet_networks bet_networks_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.bet_networks
    ADD CONSTRAINT bet_networks_pkey PRIMARY KEY (id);


--
-- Name: old_bet_tables_card_types_backup bet_tables_card_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.old_bet_tables_card_types_backup
    ADD CONSTRAINT bet_tables_card_types_pkey PRIMARY KEY (id);


--
-- Name: bet_tables bet_tables_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.bet_tables
    ADD CONSTRAINT bet_tables_pkey PRIMARY KEY (id);


--
-- Name: visa_bets bets_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.visa_bets
    ADD CONSTRAINT bets_pkey PRIMARY KEY (id);


--
-- Name: old_bets_backup bets_pkey1; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.old_bets_backup
    ADD CONSTRAINT bets_pkey1 PRIMARY KEY (id);


--
-- Name: bets bets_pkey2; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.bets
    ADD CONSTRAINT bets_pkey2 PRIMARY KEY (id);


--
-- Name: brands brands_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.brands
    ADD CONSTRAINT brands_pkey PRIMARY KEY (id);


--
-- Name: cancellation_fees cancellation_fees_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.cancellation_fees
    ADD CONSTRAINT cancellation_fees_pkey PRIMARY KEY (id);


--
-- Name: card_types card_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.card_types
    ADD CONSTRAINT card_types_pkey PRIMARY KEY (id);


--
-- Name: change_types change_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.change_types
    ADD CONSTRAINT change_types_pkey PRIMARY KEY (id);


--
-- Name: check_guarantee_providers check_guarantee_providers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.check_guarantee_providers
    ADD CONSTRAINT check_guarantee_providers_pkey PRIMARY KEY (id);


--
-- Name: check_guarantee_service_types check_guarantee_service_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.check_guarantee_service_types
    ADD CONSTRAINT check_guarantee_service_types_pkey PRIMARY KEY (id);


--
-- Name: check_guarantees check_guarantees_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.check_guarantees
    ADD CONSTRAINT check_guarantees_pkey PRIMARY KEY (id);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: commission_fees commission_fees_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.commission_fees
    ADD CONSTRAINT commission_fees_pkey PRIMARY KEY (id);


--
-- Name: commission_pricings commission_pricings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.commission_pricings
    ADD CONSTRAINT commission_pricings_pkey PRIMARY KEY (id);


--
-- Name: commission_reports commission_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.commission_reports
    ADD CONSTRAINT commission_reports_pkey PRIMARY KEY (id);


--
-- Name: control_scan_merchant_sync_results control_scan_merchant_sync_results_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.control_scan_merchant_sync_results
    ADD CONSTRAINT control_scan_merchant_sync_results_pkey PRIMARY KEY (id);


--
-- Name: debit_acquirers debit_acquirers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.debit_acquirers
    ADD CONSTRAINT debit_acquirers_pkey PRIMARY KEY (id);


--
-- Name: debits debits_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.debits
    ADD CONSTRAINT debits_pkey PRIMARY KEY (id);


--
-- Name: discover_bets discover_bets_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.discover_bets
    ADD CONSTRAINT discover_bets_pkey PRIMARY KEY (id);


--
-- Name: discover_user_bet_tables discover_user_bet_tables_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.discover_user_bet_tables
    ADD CONSTRAINT discover_user_bet_tables_pkey PRIMARY KEY (id);


--
-- Name: discovers discovers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.discovers
    ADD CONSTRAINT discovers_pkey PRIMARY KEY (id);


--
-- Name: ebts ebts_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.ebts
    ADD CONSTRAINT ebts_pkey PRIMARY KEY (id);


--
-- Name: entities entities_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.entities
    ADD CONSTRAINT entities_pkey PRIMARY KEY (id);


--
-- Name: equipment_costs equipment_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.equipment_costs
    ADD CONSTRAINT equipment_costs_pkey PRIMARY KEY (id);


--
-- Name: equipment_items equipment_items_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.equipment_items
    ADD CONSTRAINT equipment_items_pkey PRIMARY KEY (id);


--
-- Name: equipment_programming_type_xrefs equipment_programming_type_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.equipment_programming_type_xrefs
    ADD CONSTRAINT equipment_programming_type_xrefs_pkey PRIMARY KEY (id, programming_type);


--
-- Name: equipment_programmings equipment_programmings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.equipment_programmings
    ADD CONSTRAINT equipment_programmings_pkey PRIMARY KEY (id);


--
-- Name: equipment_types equipment_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.equipment_types
    ADD CONSTRAINT equipment_types_pkey PRIMARY KEY (id);


--
-- Name: external_record_fields external_record_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.external_record_fields
    ADD CONSTRAINT external_record_fields_pkey PRIMARY KEY (id);


--
-- Name: gateway0s gateway0s_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.gateway0s
    ADD CONSTRAINT gateway0s_pkey PRIMARY KEY (id);


--
-- Name: gateway1s gateway1s_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.gateway1s
    ADD CONSTRAINT gateway1s_pkey PRIMARY KEY (id);


--
-- Name: gateway2s gateway2s_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.gateway2s
    ADD CONSTRAINT gateway2s_pkey PRIMARY KEY (id);


--
-- Name: gateway_cost_structures gateway_cost_structures_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.gateway_cost_structures
    ADD CONSTRAINT gateway_cost_structures_pkey PRIMARY KEY (id);


--
-- Name: gateways gateways_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.gateways
    ADD CONSTRAINT gateways_pkey PRIMARY KEY (id);


--
-- Name: gift_card_providers gift_card_providers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.gift_card_providers
    ADD CONSTRAINT gift_card_providers_pkey PRIMARY KEY (id);


--
-- Name: gift_cards gift_cards_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.gift_cards
    ADD CONSTRAINT gift_cards_pkey PRIMARY KEY (id);


--
-- Name: groups groups_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.groups
    ADD CONSTRAINT groups_pkey PRIMARY KEY (id);


--
-- Name: imported_data_collections imported_data_collections_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.imported_data_collections
    ADD CONSTRAINT imported_data_collections_pkey PRIMARY KEY (id);


--
-- Name: invoice_items invoice_items_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.invoice_items
    ADD CONSTRAINT invoice_items_pkey PRIMARY KEY (id);


--
-- Name: job_statuses job_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.job_statuses
    ADD CONSTRAINT job_statuses_pkey PRIMARY KEY (id);


--
-- Name: last_deposit_reports last_deposit_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.last_deposit_reports
    ADD CONSTRAINT last_deposit_reports_pkey PRIMARY KEY (id);


--
-- Name: loggable_logs loggable_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.loggable_logs
    ADD CONSTRAINT loggable_logs_pkey PRIMARY KEY (id);


--
-- Name: merchant_ach_app_statuses merchant_ach_app_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_ach_app_statuses
    ADD CONSTRAINT merchant_ach_app_statuses_pkey PRIMARY KEY (id);


--
-- Name: merchant_ach_billing_options merchant_ach_billing_options_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_ach_billing_options
    ADD CONSTRAINT merchant_ach_billing_options_pkey PRIMARY KEY (id);


--
-- Name: merchant_ach_reasons merchant_ach_reasons_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_ach_reasons
    ADD CONSTRAINT merchant_ach_reasons_pkey PRIMARY KEY (id);


--
-- Name: merchant_aches merchant_achs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_aches
    ADD CONSTRAINT merchant_achs_pkey PRIMARY KEY (id);


--
-- Name: merchant_acquirers merchant_acquirers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_acquirers
    ADD CONSTRAINT merchant_acquirers_pkey PRIMARY KEY (id);


--
-- Name: merchant_banks merchant_banks_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_banks
    ADD CONSTRAINT merchant_banks_pkey PRIMARY KEY (id);


--
-- Name: merchant_bins merchant_bins_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_bins
    ADD CONSTRAINT merchant_bins_pkey PRIMARY KEY (id);


--
-- Name: merchant_cancellation_subreasons merchant_cancellation_subreasons_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_cancellation_subreasons
    ADD CONSTRAINT merchant_cancellation_subreasons_pkey PRIMARY KEY (id);


--
-- Name: merchant_cancellations_histories merchant_cancellations_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_cancellations_histories
    ADD CONSTRAINT merchant_cancellations_histories_pkey PRIMARY KEY (id);


--
-- Name: merchant_cancellations merchant_cancellations_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_cancellations
    ADD CONSTRAINT merchant_cancellations_pkey PRIMARY KEY (id);


--
-- Name: merchant_card_types merchant_card_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_card_types
    ADD CONSTRAINT merchant_card_types_pkey PRIMARY KEY (id);


--
-- Name: merchant_changes merchant_changes_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_changes
    ADD CONSTRAINT merchant_changes_pkey PRIMARY KEY (id);


--
-- Name: merchant_gateways_archives merchant_gateways_archives_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_gateways_archives
    ADD CONSTRAINT merchant_gateways_archives_pkey PRIMARY KEY (id);


--
-- Name: merchant_notes merchant_notes_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_notes
    ADD CONSTRAINT merchant_notes_pkey PRIMARY KEY (id);


--
-- Name: merchant_owners merchant_owners_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_owners
    ADD CONSTRAINT merchant_owners_pkey PRIMARY KEY (id);


--
-- Name: merchant_pcis merchant_pcis_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_pcis
    ADD CONSTRAINT merchant_pcis_pkey PRIMARY KEY (id);


--
-- Name: merchant_pricing_archives merchant_pricing_archives_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_pricing_archives
    ADD CONSTRAINT merchant_pricing_archives_pkey PRIMARY KEY (id);


--
-- Name: merchant_pricings merchant_pricings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_pricings
    ADD CONSTRAINT merchant_pricings_pkey PRIMARY KEY (id);


--
-- Name: merchant_references merchant_references_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_references
    ADD CONSTRAINT merchant_references_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_lines merchant_reject_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_reject_lines
    ADD CONSTRAINT merchant_reject_lines_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_recurrances merchant_reject_recurrances_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_reject_recurrances
    ADD CONSTRAINT merchant_reject_recurrances_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_statuses merchant_reject_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_reject_statuses
    ADD CONSTRAINT merchant_reject_statuses_pkey PRIMARY KEY (id);


--
-- Name: merchant_reject_types merchant_reject_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_reject_types
    ADD CONSTRAINT merchant_reject_types_pkey PRIMARY KEY (id);


--
-- Name: merchant_rejects merchant_rejects_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_rejects
    ADD CONSTRAINT merchant_rejects_pkey PRIMARY KEY (id);


--
-- Name: merchant_types merchant_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_types
    ADD CONSTRAINT merchant_types_pkey PRIMARY KEY (id);


--
-- Name: merchant_uw_final_approveds merchant_uw_final_approveds_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_uw_final_approveds
    ADD CONSTRAINT merchant_uw_final_approveds_pkey PRIMARY KEY (id);


--
-- Name: merchant_uw_final_statuses merchant_uw_final_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_uw_final_statuses
    ADD CONSTRAINT merchant_uw_final_statuses_pkey PRIMARY KEY (id);


--
-- Name: merchant_uw_volumes merchant_uw_volumes_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_uw_volumes
    ADD CONSTRAINT merchant_uw_volumes_pkey PRIMARY KEY (id);


--
-- Name: merchant_uws merchant_uws_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchant_uws
    ADD CONSTRAINT merchant_uws_pkey PRIMARY KEY (id);


--
-- Name: merchants merchants_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.merchants
    ADD CONSTRAINT merchants_pkey PRIMARY KEY (id);


--
-- Name: networks networks_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.networks
    ADD CONSTRAINT networks_pkey PRIMARY KEY (id);


--
-- Name: non_taxable_reasons non_taxable_reasons_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.non_taxable_reasons
    ADD CONSTRAINT non_taxable_reasons_pkey PRIMARY KEY (id);


--
-- Name: note_types note_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.note_types
    ADD CONSTRAINT note_types_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_api_logs onlineapp_api_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_api_logs
    ADD CONSTRAINT onlineapp_api_logs_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_apips onlineapp_apips_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_apips
    ADD CONSTRAINT onlineapp_apips_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_coversheets onlineapp_application_id_key; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_coversheets
    ADD CONSTRAINT onlineapp_application_id_key UNIQUE (onlineapp_application_id);


--
-- Name: onlineapp_applications onlineapp_applications_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_applications
    ADD CONSTRAINT onlineapp_applications_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobranded_application_aches onlineapp_cobranded_application_aches_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobranded_application_aches
    ADD CONSTRAINT onlineapp_cobranded_application_aches_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobranded_application_values onlineapp_cobranded_application_values_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobranded_application_values
    ADD CONSTRAINT onlineapp_cobranded_application_values_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobranded_applications onlineapp_cobranded_applications_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobranded_applications
    ADD CONSTRAINT onlineapp_cobranded_applications_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_cobrands onlineapp_cobrands_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_cobrands
    ADD CONSTRAINT onlineapp_cobrands_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_coversheets onlineapp_coversheets_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_coversheets
    ADD CONSTRAINT onlineapp_coversheets_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_email_timeline_subjects onlineapp_email_timeline_subjects_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_email_timeline_subjects
    ADD CONSTRAINT onlineapp_email_timeline_subjects_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_email_timelines onlineapp_email_timelines_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_email_timelines
    ADD CONSTRAINT onlineapp_email_timelines_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_epayments onlineapp_epayments_merchant_id_key; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_merchant_id_key UNIQUE (merchant_id);


--
-- Name: onlineapp_epayments onlineapp_epayments_onlineapp_applications_id_key; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_onlineapp_applications_id_key UNIQUE (onlineapp_application_id);


--
-- Name: onlineapp_epayments onlineapp_epayments_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_epayments
    ADD CONSTRAINT onlineapp_epayments_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_groups onlineapp_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_groups
    ADD CONSTRAINT onlineapp_groups_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_multipasses onlineapp_multipasses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_multipasses
    ADD CONSTRAINT onlineapp_multipasses_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_settings onlineapp_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_settings
    ADD CONSTRAINT onlineapp_settings_pkey PRIMARY KEY (key);


--
-- Name: onlineapp_template_fields onlineapp_template_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_template_fields
    ADD CONSTRAINT onlineapp_template_fields_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_template_pages onlineapp_template_pages_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_template_pages
    ADD CONSTRAINT onlineapp_template_pages_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_template_sections onlineapp_template_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_template_sections
    ADD CONSTRAINT onlineapp_template_sections_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_templates onlineapp_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_templates
    ADD CONSTRAINT onlineapp_templates_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_users onlineapp_users_email_key; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users
    ADD CONSTRAINT onlineapp_users_email_key UNIQUE (email);


--
-- Name: onlineapp_users_managers onlineapp_users_managers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_managers
    ADD CONSTRAINT onlineapp_users_managers_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_users_onlineapp_cobrands onlineapp_users_onlineapp_cobrands_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_cobrands
    ADD CONSTRAINT onlineapp_users_onlineapp_cobrands_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_users_onlineapp_templates onlineapp_users_onlineapp_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_templates
    ADD CONSTRAINT onlineapp_users_onlineapp_templates_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_users onlineapp_users_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users
    ADD CONSTRAINT onlineapp_users_pkey PRIMARY KEY (id);


--
-- Name: orderitem_types orderitem_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.orderitem_types
    ADD CONSTRAINT orderitem_types_pkey PRIMARY KEY (id);


--
-- Name: orderitems orderitems_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.orderitems
    ADD CONSTRAINT orderitems_pkey PRIMARY KEY (id);


--
-- Name: orderitems_replacements orderitems_replacements_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.orderitems_replacements
    ADD CONSTRAINT orderitems_replacements_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: original_acquirers original_acquirers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.original_acquirers
    ADD CONSTRAINT original_acquirers_pkey PRIMARY KEY (id);


--
-- Name: partners partners_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.partners
    ADD CONSTRAINT partners_pkey PRIMARY KEY (id);


--
-- Name: pci_billing_histories pci_billing_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.pci_billing_histories
    ADD CONSTRAINT pci_billing_histories_pkey PRIMARY KEY (id);


--
-- Name: pci_billing_types pci_billing_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.pci_billing_types
    ADD CONSTRAINT pci_billing_types_pkey PRIMARY KEY (id);


--
-- Name: pci_billings pci_billings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.pci_billings
    ADD CONSTRAINT pci_billings_pkey PRIMARY KEY (id);


--
-- Name: pci_compliance_date_types pci_compliance_date_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.pci_compliance_date_types
    ADD CONSTRAINT pci_compliance_date_types_pkey PRIMARY KEY (id);


--
-- Name: pci_compliance_status_logs pci_compliance_status_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.pci_compliance_status_logs
    ADD CONSTRAINT pci_compliance_status_logs_pkey PRIMARY KEY (id);


--
-- Name: pci_compliances pci_compliances_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.pci_compliances
    ADD CONSTRAINT pci_compliances_pkey PRIMARY KEY (id);


--
-- Name: permission_caches permission_caches_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.permission_caches
    ADD CONSTRAINT permission_caches_pkey PRIMARY KEY (id);


--
-- Name: permission_constraints permission_constraints_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.permission_constraints
    ADD CONSTRAINT permission_constraints_pkey PRIMARY KEY (id);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: permissions_roles permissions_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.permissions_roles
    ADD CONSTRAINT permissions_roles_pkey PRIMARY KEY (id);


--
-- Name: posts posts_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_pkey PRIMARY KEY (id);


--
-- Name: pricing_matrices pricing_matrices_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.pricing_matrices
    ADD CONSTRAINT pricing_matrices_pkey PRIMARY KEY (id);


--
-- Name: product_categories product_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_pkey PRIMARY KEY (id);


--
-- Name: products_and_services products_and_services_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.products_and_services
    ADD CONSTRAINT products_and_services_pkey PRIMARY KEY (id);


--
-- Name: products_services_types products_services_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.products_services_types
    ADD CONSTRAINT products_services_types_pkey PRIMARY KEY (id);


--
-- Name: profit_projections profit_projections_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.profit_projections
    ADD CONSTRAINT profit_projections_pkey PRIMARY KEY (id);


--
-- Name: profitability_reports profitability_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.profitability_reports
    ADD CONSTRAINT profitability_reports_pkey PRIMARY KEY (id);


--
-- Name: referer_products_services_xrefs referer_products_services_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.referer_products_services_xrefs
    ADD CONSTRAINT referer_products_services_xrefs_pkey PRIMARY KEY (id);


--
-- Name: referers_bets referers_bets_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.referers_bets
    ADD CONSTRAINT referers_bets_pkey PRIMARY KEY (id);


--
-- Name: referers referers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.referers
    ADD CONSTRAINT referers_pkey PRIMARY KEY (id);


--
-- Name: rep_cost_structures rep_cost_structures_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.rep_cost_structures
    ADD CONSTRAINT rep_cost_structures_pkey PRIMARY KEY (id);


--
-- Name: rep_monthly_costs rep_monthly_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.rep_monthly_costs
    ADD CONSTRAINT rep_monthly_costs_pkey PRIMARY KEY (id);


--
-- Name: rep_partner_xrefs rep_partner_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.rep_partner_xrefs
    ADD CONSTRAINT rep_partner_xrefs_pkey PRIMARY KEY (id);


--
-- Name: rep_product_profit_pcts rep_product_profit_pcts_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.rep_product_profit_pcts
    ADD CONSTRAINT rep_product_profit_pcts_pkey PRIMARY KEY (id);


--
-- Name: rep_product_settings rep_product_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.rep_product_settings
    ADD CONSTRAINT rep_product_settings_pkey PRIMARY KEY (id);


--
-- Name: residual_parameter_types residual_parameter_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_parameter_types
    ADD CONSTRAINT residual_parameter_types_pkey PRIMARY KEY (id);


--
-- Name: residual_parameters residual_parameters_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_parameters
    ADD CONSTRAINT residual_parameters_pkey PRIMARY KEY (id);


--
-- Name: residual_pricings residual_pricings_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_pricings
    ADD CONSTRAINT residual_pricings_pkey PRIMARY KEY (id);


--
-- Name: residual_product_controls residual_product_controls_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_product_controls
    ADD CONSTRAINT residual_product_controls_pkey PRIMARY KEY (id);


--
-- Name: residual_reports residual_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_reports
    ADD CONSTRAINT residual_reports_pkey PRIMARY KEY (id);


--
-- Name: residual_time_factors residual_time_factors_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_time_factors
    ADD CONSTRAINT residual_time_factors_pkey PRIMARY KEY (id);


--
-- Name: residual_time_parameters residual_time_parameters_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_time_parameters
    ADD CONSTRAINT residual_time_parameters_pkey PRIMARY KEY (id);


--
-- Name: residual_volume_tiers residual_volume_tiers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.residual_volume_tiers
    ADD CONSTRAINT residual_volume_tiers_pkey PRIMARY KEY (id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sales_goal_archives sales_goal_archives_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.sales_goal_archives
    ADD CONSTRAINT sales_goal_archives_pkey PRIMARY KEY (id);


--
-- Name: sales_goals sales_goals_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.sales_goals
    ADD CONSTRAINT sales_goals_pkey PRIMARY KEY (id);


--
-- Name: saq_answers saq_answers_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_answers
    ADD CONSTRAINT saq_answers_pkey PRIMARY KEY (id);


--
-- Name: saq_control_scan_unboardeds saq_control_scan_unboardeds_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_control_scan_unboardeds
    ADD CONSTRAINT saq_control_scan_unboardeds_pkey PRIMARY KEY (id);


--
-- Name: saq_control_scans saq_control_scans_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_control_scans
    ADD CONSTRAINT saq_control_scans_pkey PRIMARY KEY (id);


--
-- Name: saq_merchant_pci_email_sents saq_merchant_pci_email_sents_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_merchant_pci_email_sents
    ADD CONSTRAINT saq_merchant_pci_email_sents_pkey PRIMARY KEY (id);


--
-- Name: saq_merchant_pci_emails saq_merchant_pci_emails_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_merchant_pci_emails
    ADD CONSTRAINT saq_merchant_pci_emails_pkey PRIMARY KEY (id);


--
-- Name: saq_merchant_survey_xrefs saq_merchant_survey_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_merchant_survey_xrefs
    ADD CONSTRAINT saq_merchant_survey_xrefs_pkey PRIMARY KEY (id);


--
-- Name: saq_merchants saq_merchants_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_merchants
    ADD CONSTRAINT saq_merchants_pkey PRIMARY KEY (id);


--
-- Name: saq_prequalifications saq_prequalifications_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_prequalifications
    ADD CONSTRAINT saq_prequalifications_pkey PRIMARY KEY (id);


--
-- Name: saq_questions saq_questions_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_questions
    ADD CONSTRAINT saq_questions_pkey PRIMARY KEY (id);


--
-- Name: saq_survey_question_xrefs saq_survey_question_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_survey_question_xrefs
    ADD CONSTRAINT saq_survey_question_xrefs_pkey PRIMARY KEY (id);


--
-- Name: saq_surveys saq_surveys_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.saq_surveys
    ADD CONSTRAINT saq_surveys_pkey PRIMARY KEY (id);


--
-- Name: schema_migrations schema_migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.schema_migrations
    ADD CONSTRAINT schema_migrations_pkey PRIMARY KEY (id);


--
-- Name: shipping_type_items shipping_type_items_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.shipping_type_items
    ADD CONSTRAINT shipping_type_items_pkey PRIMARY KEY (id);


--
-- Name: shipping_types shipping_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.shipping_types
    ADD CONSTRAINT shipping_types_pkey PRIMARY KEY (id);


--
-- Name: sponsor_banks sponsor_banks_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.sponsor_banks
    ADD CONSTRAINT sponsor_banks_pkey PRIMARY KEY (id);


--
-- Name: system_transactions system_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.system_transactions
    ADD CONSTRAINT system_transactions_pkey PRIMARY KEY (id);


--
-- Name: tgates tgates_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.tgates
    ADD CONSTRAINT tgates_pkey PRIMARY KEY (id);


--
-- Name: tickler_comments tickler_comments_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.tickler_comments
    ADD CONSTRAINT tickler_comments_pkey PRIMARY KEY (id);


--
-- Name: tickler_leads_logs tickler_leads_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.tickler_leads_logs
    ADD CONSTRAINT tickler_leads_logs_pkey PRIMARY KEY (id);


--
-- Name: timeline_entries timeline_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.timeline_entries
    ADD CONSTRAINT timeline_entries_pkey PRIMARY KEY (id);


--
-- Name: timeline_items timeline_items_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.timeline_items
    ADD CONSTRAINT timeline_items_pkey PRIMARY KEY (id);


--
-- Name: onlineapp_users token; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users
    ADD CONSTRAINT token UNIQUE (token);


--
-- Name: transaction_types transaction_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.transaction_types
    ADD CONSTRAINT transaction_types_pkey PRIMARY KEY (id);


--
-- Name: usaepay_rep_gtwy_add_costs usaepay_rep_gtwy_add_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.usaepay_rep_gtwy_add_costs
    ADD CONSTRAINT usaepay_rep_gtwy_add_costs_pkey PRIMARY KEY (id);


--
-- Name: usaepay_rep_gtwy_costs usaepay_rep_gtwy_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.usaepay_rep_gtwy_costs
    ADD CONSTRAINT usaepay_rep_gtwy_costs_pkey PRIMARY KEY (id);


--
-- Name: user_bet_tables user_bet_tables_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_bet_tables
    ADD CONSTRAINT user_bet_tables_pkey PRIMARY KEY (id);


--
-- Name: user_compensation_profiles user_compensation_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_compensation_profiles
    ADD CONSTRAINT user_compensation_profiles_pkey PRIMARY KEY (id);


--
-- Name: user_costs_archives user_costs_archives_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_costs_archives
    ADD CONSTRAINT user_costs_archives_pkey PRIMARY KEY (id);


--
-- Name: user_parameter_types user_parameter_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_parameter_types
    ADD CONSTRAINT user_parameter_types_pkey PRIMARY KEY (id);


--
-- Name: user_parameters user_parameters_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_parameters
    ADD CONSTRAINT user_parameters_pkey PRIMARY KEY (id);


--
-- Name: user_residual_options user_residual_options_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_residual_options
    ADD CONSTRAINT user_residual_options_pkey PRIMARY KEY (id);


--
-- Name: user_types user_types_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_types
    ADD CONSTRAINT user_types_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users_products_risks users_products_risks_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.users_products_risks
    ADD CONSTRAINT users_products_risks_pkey PRIMARY KEY (id);


--
-- Name: users_roles users_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.users_roles
    ADD CONSTRAINT users_roles_pkey PRIMARY KEY (id);


--
-- Name: uw_approvalinfo_merchant_xrefs uw_approvalinfo_merchant_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_approvalinfo_merchant_xrefs
    ADD CONSTRAINT uw_approvalinfo_merchant_xrefs_pkey PRIMARY KEY (id);


--
-- Name: uw_approvalinfos uw_approvalinfos_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_approvalinfos
    ADD CONSTRAINT uw_approvalinfos_pkey PRIMARY KEY (id);


--
-- Name: uw_infodoc_merchant_xrefs uw_infodoc_merchant_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_infodoc_merchant_xrefs
    ADD CONSTRAINT uw_infodoc_merchant_xrefs_pkey PRIMARY KEY (id);


--
-- Name: uw_infodocs uw_infodocs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_infodocs
    ADD CONSTRAINT uw_infodocs_pkey PRIMARY KEY (id);


--
-- Name: uw_receiveds uw_receiveds_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_receiveds
    ADD CONSTRAINT uw_receiveds_pkey PRIMARY KEY (id);


--
-- Name: uw_status_merchant_xrefs uw_status_merchant_xrefs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_status_merchant_xrefs
    ADD CONSTRAINT uw_status_merchant_xrefs_pkey PRIMARY KEY (id);


--
-- Name: uw_statuses uw_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_statuses
    ADD CONSTRAINT uw_statuses_pkey PRIMARY KEY (id);


--
-- Name: uw_verified_options uw_verified_options_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.uw_verified_options
    ADD CONSTRAINT uw_verified_options_pkey PRIMARY KEY (id);


--
-- Name: vendors vendors_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.vendors
    ADD CONSTRAINT vendors_pkey PRIMARY KEY (id);


--
-- Name: virtual_check_webs virtual_check_webs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.virtual_check_webs
    ADD CONSTRAINT virtual_check_webs_pkey PRIMARY KEY (id);


--
-- Name: virtual_checks virtual_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.virtual_checks
    ADD CONSTRAINT virtual_checks_pkey PRIMARY KEY (id);


--
-- Name: warranties warranties_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.warranties
    ADD CONSTRAINT warranties_pkey PRIMARY KEY (id);


--
-- Name: web_ach_rep_costs web_ach_rep_costs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.web_ach_rep_costs
    ADD CONSTRAINT web_ach_rep_costs_pkey PRIMARY KEY (id);


--
-- Name: webpasses webpasses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.webpasses
    ADD CONSTRAINT webpasses_pkey PRIMARY KEY (id);


--
-- Name: womply_actions womply_actions_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.womply_actions
    ADD CONSTRAINT womply_actions_pkey PRIMARY KEY (id);


--
-- Name: womply_merchant_logs womply_merchant_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.womply_merchant_logs
    ADD CONSTRAINT womply_merchant_logs_pkey PRIMARY KEY (id);


--
-- Name: womply_statuses womply_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.womply_statuses
    ADD CONSTRAINT womply_statuses_pkey PRIMARY KEY (id);


--
-- Name: ach_merchantid; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ach_merchantid ON public.aches USING btree (merchant_id);


--
-- Name: ach_mid; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ach_mid ON public.aches USING btree (ach_mid);


--
-- Name: ach_rep_cost_ach_providers_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ach_rep_cost_ach_providers_index ON public.ach_rep_costs USING btree (ach_provider_id);


--
-- Name: ach_rep_cost_user_comp_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ach_rep_cost_user_comp_index ON public.ach_rep_costs USING btree (user_compensation_profile_id);


--
-- Name: achs_merchant_id_id_oldx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX achs_merchant_id_id_oldx ON public.aches USING btree (merchant_id_old);


--
-- Name: achs_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX achs_merchant_id_idx ON public.aches USING btree (merchant_id);


--
-- Name: action_taken; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX action_taken ON public.tickler_leads_logs USING btree (action);


--
-- Name: addl_amex_rep_cost_user_comp_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX addl_amex_rep_cost_user_comp_index ON public.addl_amex_rep_costs USING btree (user_compensation_profile_id);


--
-- Name: address_owner_uidx; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX address_owner_uidx ON public.addresses USING btree (merchant_owner_id);


--
-- Name: addresses_address_id_id_oldx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX addresses_address_id_id_oldx ON public.addresses USING btree (address_id_old);


--
-- Name: addresses_address_type_id_oldx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX addresses_address_type_id_oldx ON public.addresses USING btree (address_type_old);


--
-- Name: addresses_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX addresses_merchant_id_idx ON public.addresses USING btree (merchant_id);


--
-- Name: addresses_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX addresses_merchant_id_old_idx ON public.addresses USING btree (merchant_id_old);


--
-- Name: addresses_merchant_owner_id_id_oldx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX addresses_merchant_owner_id_id_oldx ON public.addresses USING btree (merchant_owner_id_old);


--
-- Name: addresses_merchant_type_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX addresses_merchant_type_idx ON public.addresses USING btree (address_type_id, merchant_id);


--
-- Name: adjustments_user_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX adjustments_user_idx ON public.adjustments USING btree (user_id);


--
-- Name: admin_entity_view_entity_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX admin_entity_view_entity_id_idx ON public.admin_entity_views USING btree (entity_id);


--
-- Name: admin_entity_view_entity_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX admin_entity_view_entity_old_idx ON public.admin_entity_views USING btree (entity_old);


--
-- Name: admin_entity_view_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX admin_entity_view_user_id_idx ON public.admin_entity_views USING btree (user_id);


--
-- Name: admin_entity_view_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX admin_entity_view_user_id_old_idx ON public.admin_entity_views USING btree (user_id_old);


--
-- Name: amexes_merchant_id_id_oldx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX amexes_merchant_id_id_oldx ON public.amexes USING btree (merchant_id_old);


--
-- Name: amexes_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX amexes_merchant_id_idx ON public.amexes USING btree (merchant_id);


--
-- Name: api_config_name; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX api_config_name ON public.api_configurations USING btree (configuration_name);


--
-- Name: api_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX api_idx ON public.onlineapp_applications USING btree (api);


--
-- Name: app_statuses_merchant_ach_app_status_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX app_statuses_merchant_ach_app_status_id_idx ON public.app_statuses USING btree (merchant_ach_app_status_id);


--
-- Name: application_id_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX application_id_key ON public.onlineapp_multipasses USING btree (application_id);


--
-- Name: approved; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX approved ON public.tickler_comments USING btree (approved);


--
-- Name: assoc_ext_rec_merchant_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX assoc_ext_rec_merchant_id ON public.associated_external_records USING btree (merchant_id);


--
-- Name: authorizes_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX authorizes_merchant_id_idx ON public.authorizes USING btree (merchant_id);


--
-- Name: bankcards_bcmid; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_bcmid ON public.bankcards USING btree (bc_mid);


--
-- Name: bankcards_bet_code; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_bet_code ON public.bankcards USING btree (bet_code_old);


--
-- Name: bankcards_bet_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_bet_id ON public.bankcards USING btree (bet_id);


--
-- Name: bankcards_bgwi_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_bgwi_old_index ON public.bankcards USING btree (bc_gw_gateway_id_old);


--
-- Name: bankcards_gateway_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_gateway_id_index ON public.bankcards USING btree (gateway_id);


--
-- Name: bankcards_merchant_id__oldidx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_merchant_id__oldidx ON public.bankcards USING btree (merchant_id_old);


--
-- Name: bankcards_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_merchant_id_idx ON public.bankcards USING btree (merchant_id);


--
-- Name: bankcards_urgac_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_urgac_index ON public.bankcards USING btree (usaepay_rep_gtwy_add_cost_id);


--
-- Name: bankcards_urgac_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_urgac_old_index ON public.bankcards USING btree (bc_usaepay_rep_gtwy_add_cost_id_old);


--
-- Name: bankcards_urgc_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_urgc_index ON public.bankcards USING btree (usaepay_rep_gtwy_cost_id);


--
-- Name: bankcards_urgc_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bankcards_urgc_old_index ON public.bankcards USING btree (bc_usaepay_rep_gtwy_cost_id_old);


--
-- Name: bg_jobs_modified; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bg_jobs_modified ON public.background_jobs USING btree (modified);


--
-- Name: bg_jobs_status_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX bg_jobs_status_id ON public.background_jobs USING btree (job_status_id);


--
-- Name: check_guarantees_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX check_guarantees_merchant_id_idx ON public.check_guarantees USING btree (merchant_id);


--
-- Name: cobranded_application_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX cobranded_application_id_idx ON public.onlineapp_cobranded_application_values USING btree (cobranded_application_id);


--
-- Name: comment_type; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX comment_type ON public.tickler_comments USING btree (comment_type);


--
-- Name: commission_fees_associated_user_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_fees_associated_user_id ON public.commission_fees USING btree (associated_user_id);


--
-- Name: commission_pricing_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_bet_code_old_idx ON public.commission_pricings USING btree (bet_code_old);


--
-- Name: commission_pricing_bet_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_bet_id_idx ON public.commission_pricings USING btree (bet_table_id);


--
-- Name: commission_pricing_index3; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_index3 ON public.commission_pricings USING btree (c_month);


--
-- Name: commission_pricing_index4; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_index4 ON public.commission_pricings USING btree (c_year);


--
-- Name: commission_pricing_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_merchant_id_idx ON public.commission_pricings USING btree (merchant_id);


--
-- Name: commission_pricing_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_merchant_id_old_idx ON public.commission_pricings USING btree (merchant_id_old);


--
-- Name: commission_pricing_products_services_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_products_services_type_id_idx ON public.commission_pricings USING btree (products_services_type_id);


--
-- Name: commission_pricing_products_services_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_products_services_type_id_old_idx ON public.commission_pricings USING btree (products_services_type_id_old);


--
-- Name: commission_pricing_ref_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_ref_seq_number_old_idx ON public.commission_pricings USING btree (ref_seq_number_old);


--
-- Name: commission_pricing_referer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_referer_id_idx ON public.commission_pricings USING btree (referer_id);


--
-- Name: commission_pricing_res_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_res_seq_number_old_idx ON public.commission_pricings USING btree (res_seq_number_old);


--
-- Name: commission_pricing_reseller_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_reseller_id_idx ON public.commission_pricings USING btree (reseller_id);


--
-- Name: commission_pricing_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_user_id_idx ON public.commission_pricings USING btree (user_id);


--
-- Name: commission_pricing_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_pricing_user_id_old_idx ON public.commission_pricings USING btree (user_id_old);


--
-- Name: commission_report_ach_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_ach_seq_number_old_idx ON public.commission_reports USING btree (ach_seq_number_old);


--
-- Name: commission_report_end_date_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_end_date_idx ON public.commission_reports USING btree (public.make_date(c_year, c_month, 28));


--
-- Name: commission_report_merchant_ach_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_merchant_ach_id_idx ON public.commission_reports USING btree (merchant_ach_id);


--
-- Name: commission_report_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_merchant_id_idx ON public.commission_reports USING btree (merchant_id);


--
-- Name: commission_report_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_merchant_id_old_idx ON public.commission_reports USING btree (merchant_id_old);


--
-- Name: commission_report_order_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_order_id_idx ON public.commission_reports USING btree (order_id);


--
-- Name: commission_report_order_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_order_id_old_idx ON public.commission_reports USING btree (order_id_old);


--
-- Name: commission_report_partner_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_partner_id_idx ON public.commission_reports USING btree (partner_id);


--
-- Name: commission_report_partner_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_partner_id_old_idx ON public.commission_reports USING btree (partner_id_old);


--
-- Name: commission_report_ref_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_ref_seq_number_old_idx ON public.commission_reports USING btree (ref_seq_number_old);


--
-- Name: commission_report_referer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_referer_id_idx ON public.commission_reports USING btree (referer_id);


--
-- Name: commission_report_res_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_res_seq_number_old_idx ON public.commission_reports USING btree (res_seq_number_old);


--
-- Name: commission_report_reseller_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_reseller_id_idx ON public.commission_reports USING btree (reseller_id);


--
-- Name: commission_report_shipping_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_shipping_type_id_idx ON public.commission_reports USING btree (shipping_type_id);


--
-- Name: commission_report_start_date_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_start_date_idx ON public.commission_reports USING btree (public.make_date(c_year, c_month, 1));


--
-- Name: commission_report_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_user_id_idx ON public.commission_reports USING btree (user_id);


--
-- Name: commission_report_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX commission_report_user_id_old_idx ON public.commission_reports USING btree (user_id_old);


--
-- Name: corp_contact_name_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX corp_contact_name_idx ON public.onlineapp_applications USING btree (corp_contact_name);


--
-- Name: created; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX created ON public.tickler_comments USING btree (created);


--
-- Name: dba_business_name_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX dba_business_name_idx ON public.onlineapp_applications USING btree (dba_business_name);


--
-- Name: debit_acquirer_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX debit_acquirer_id_old_idx ON public.debit_acquirers USING btree (debit_acquirer_id_old);


--
-- Name: debits_debit_acquirer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX debits_debit_acquirer_id_idx ON public.debits USING btree (debit_acquirer_id);


--
-- Name: debits_debit_acquirer_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX debits_debit_acquirer_id_old_idx ON public.debits USING btree (debit_acquirer_id_old);


--
-- Name: debits_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX debits_merchant_id_idx ON public.debits USING btree (merchant_id);


--
-- Name: debits_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX debits_merchant_id_old_idx ON public.debits USING btree (merchant_id_old);


--
-- Name: device_number_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX device_number_key ON public.onlineapp_multipasses USING btree (device_number);


--
-- Name: discover_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_bet_code_old_idx ON public.discovers USING btree (bet_code_old);


--
-- Name: discover_bet_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_bet_id_idx ON public.discovers USING btree (bet_id);


--
-- Name: discover_bets_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_bets_bet_code_old_idx ON public.discover_bets USING btree (bet_code_old);


--
-- Name: discover_bets_bet_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_bets_bet_id_idx ON public.discover_bets USING btree (bet_id);


--
-- Name: discover_user_bet_table_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_user_bet_table_bet_code_old_idx ON public.discover_bets USING btree (bet_code_old);


--
-- Name: discover_user_bet_table_bet_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_user_bet_table_bet_id_idx ON public.discover_bets USING btree (bet_id);


--
-- Name: discover_user_bet_table_network_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_user_bet_table_network_idx ON public.discover_user_bet_tables USING btree (network);


--
-- Name: discover_user_bet_table_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_user_bet_table_user_id_idx ON public.discover_user_bet_tables USING btree (user_id);


--
-- Name: discover_user_bet_table_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discover_user_bet_table_user_id_old_idx ON public.discover_user_bet_tables USING btree (user_id_old);


--
-- Name: discovers_gateway_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discovers_gateway_id_idx ON public.discovers USING btree (gateway_id);


--
-- Name: discovers_gateway_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discovers_gateway_id_old_idx ON public.discovers USING btree (gateway_id_old);


--
-- Name: discovers_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discovers_merchant_id_idx ON public.discovers USING btree (merchant_id);


--
-- Name: discovers_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX discovers_merchant_id_old_idx ON public.discovers USING btree (merchant_id_old);


--
-- Name: ebts_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ebts_merchant_id_idx ON public.ebts USING btree (merchant_id);


--
-- Name: ebts_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ebts_merchant_id_old_idx ON public.ebts USING btree (merchant_id_old);


--
-- Name: entities_entity_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX entities_entity_old_idx ON public.entities USING btree (entity_old);


--
-- Name: eptx_programming_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX eptx_programming_id_idx ON public.equipment_programming_type_xrefs USING btree (equipment_programming_id);


--
-- Name: eptx_programming_old_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX eptx_programming_old_id_idx ON public.equipment_programming_type_xrefs USING btree (programming_id_old);


--
-- Name: equipment_costs_equipment_item_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_costs_equipment_item_id_idx ON public.equipment_costs USING btree (equipment_item_id);


--
-- Name: equipment_item_active_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_item_active_idx ON public.equipment_items USING btree (active);


--
-- Name: equipment_item_equipment_item_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_item_equipment_item_old_idx ON public.equipment_items USING btree (equipment_item_old);


--
-- Name: equipment_item_equipment_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_item_equipment_type_id_idx ON public.equipment_items USING btree (equipment_type_id);


--
-- Name: equipment_item_equipment_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_item_equipment_type_old_idx ON public.equipment_items USING btree (equipment_type_old);


--
-- Name: equipment_item_warranty_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_item_warranty_idx ON public.equipment_items USING btree (warranty);


--
-- Name: equipment_programming_appid; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programming_appid ON public.equipment_programmings USING btree (app_type);


--
-- Name: equipment_programming_serialnum; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programming_serialnum ON public.equipment_programmings USING btree (serial_number);


--
-- Name: equipment_programming_status; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programming_status ON public.equipment_programmings USING btree (status);


--
-- Name: equipment_programming_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programming_user_id_idx ON public.equipment_programmings USING btree (user_id);


--
-- Name: equipment_programming_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programming_user_id_old_idx ON public.equipment_programmings USING btree (user_id_old);


--
-- Name: equipment_programmings_gateway_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programmings_gateway_id_idx ON public.equipment_programmings USING btree (gateway_id);


--
-- Name: equipment_programmings_gateway_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programmings_gateway_id_old_idx ON public.equipment_programmings USING btree (gateway_id_old);


--
-- Name: equipment_programmings_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programmings_merchant_id_idx ON public.equipment_programmings USING btree (merchant_id);


--
-- Name: equipment_programmings_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_programmings_merchant_id_old_idx ON public.equipment_programmings USING btree (merchant_id_old);


--
-- Name: equipment_types_equipment_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX equipment_types_equipment_type_old_idx ON public.equipment_types USING btree (equipment_type_old);


--
-- Name: external_record_fields_assoc_external_record_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX external_record_fields_assoc_external_record_id ON public.external_record_fields USING btree (associated_external_record_id);


--
-- Name: external_record_fields_merchant_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX external_record_fields_merchant_id_index ON public.external_record_fields USING btree (merchant_id);


--
-- Name: foreign_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX foreign_key ON public.tickler_comments USING btree (foreign_key);


--
-- Name: gateway0s_gw_usaepay_add_cost_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway0s_gw_usaepay_add_cost_id_idx ON public.gateway0s USING btree (gw_usaepay_rep_gtwy_add_cost_id);


--
-- Name: gateway0s_gw_usaepay_add_cost_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway0s_gw_usaepay_add_cost_id_old_idx ON public.gateway0s USING btree (gw_usaepay_rep_gtwy_add_cost_id_old);


--
-- Name: gateway0s_gw_usaepay_cost_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway0s_gw_usaepay_cost_id_idx ON public.gateway0s USING btree (gw_usaepay_rep_gtwy_cost_id);


--
-- Name: gateway0s_gw_usaepay_cost_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway0s_gw_usaepay_cost_id_old_idx ON public.gateway0s USING btree (gw_usaepay_rep_gtwy_cost_id_old);


--
-- Name: gateway0s_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway0s_merchant_id_idx ON public.gateway0s USING btree (merchant_id);


--
-- Name: gateway0s_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway0s_merchant_id_old_idx ON public.gateway0s USING btree (merchant_id_old);


--
-- Name: gateway1_pricing_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway1_pricing_index ON public.merchant_gateways_archives USING btree (gateway1_pricing_id);


--
-- Name: gateway1s_gateway_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway1s_gateway_id_idx ON public.gateway1s USING btree (gateway_id);


--
-- Name: gateway1s_gateway_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway1s_gateway_id_old_idx ON public.gateway1s USING btree (gateway_id_old);


--
-- Name: gateway1s_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway1s_merchant_id_idx ON public.gateway1s USING btree (merchant_id);


--
-- Name: gateway1s_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway1s_merchant_id_old_idx ON public.gateway1s USING btree (merchant_id_old);


--
-- Name: gateway2s_gateway_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway2s_gateway_id_idx ON public.gateway2s USING btree (gateway_id);


--
-- Name: gateway2s_gateway_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway2s_gateway_id_old_idx ON public.gateway2s USING btree (gateway_id_old);


--
-- Name: gateway2s_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway2s_merchant_id_idx ON public.gateway2s USING btree (merchant_id);


--
-- Name: gateway2s_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gateway2s_merchant_id_old_idx ON public.gateway2s USING btree (merchant_id_old);


--
-- Name: gift_cards_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gift_cards_merchant_id_idx ON public.gift_cards USING btree (merchant_id);


--
-- Name: gift_cards_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX gift_cards_merchant_id_old_idx ON public.gift_cards USING btree (merchant_id_old);


--
-- Name: hash_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX hash_idx ON public.onlineapp_applications USING btree (hash);


--
-- Name: imported_data_collections_merchant_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX imported_data_collections_merchant_id ON public.imported_data_collections USING btree (merchant_id);


--
-- Name: imported_data_collections_month; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX imported_data_collections_month ON public.imported_data_collections USING btree (month);


--
-- Name: imported_data_collections_year; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX imported_data_collections_year ON public.imported_data_collections USING btree (year);


--
-- Name: install_var_rs_document_guid_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX install_var_rs_document_guid_idx ON public.onlineapp_applications USING btree (install_var_rs_document_guid);


--
-- Name: invoice_item_merchant_ach_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX invoice_item_merchant_ach_id_index ON public.invoice_items USING btree (merchant_ach_id);


--
-- Name: is_spam; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX is_spam ON public.tickler_comments USING btree (is_spam);


--
-- Name: k; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX k ON public.bet_tables USING btree (name);


--
-- Name: language; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX language ON public.tickler_comments USING btree (language);


--
-- Name: last_deposit_reports_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX last_deposit_reports_merchant_id_idx ON public.last_deposit_reports USING btree (merchant_id);


--
-- Name: last_deposit_reports_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX last_deposit_reports_merchant_id_old_idx ON public.last_deposit_reports USING btree (merchant_id_old);


--
-- Name: last_deposit_reports_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX last_deposit_reports_user_id_idx ON public.last_deposit_reports USING btree (user_id);


--
-- Name: last_deposit_reports_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX last_deposit_reports_user_id_old_idx ON public.last_deposit_reports USING btree (user_id_old);


--
-- Name: lead; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX lead ON public.tickler_leads_logs USING btree (lead_id);


--
-- Name: legal_business_name_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX legal_business_name_idx ON public.onlineapp_applications USING btree (legal_business_name);


--
-- Name: lft; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX lft ON public.tickler_comments USING btree (lft);


--
-- Name: logged; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX logged ON public.tickler_leads_logs USING btree (created);


--
-- Name: mailing_city_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mailing_city_idx ON public.onlineapp_applications USING btree (mailing_city);


--
-- Name: merchant_ach_app_statuses_app_status_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_ach_app_statuses_app_status_id_old_idx ON public.merchant_ach_app_statuses USING btree (app_status_id_old);


--
-- Name: merchant_ach_billing_options_billing_option_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_ach_billing_options_billing_option_id_old_idx ON public.merchant_ach_billing_options USING btree (billing_option_id_old);


--
-- Name: merchant_achs_ach_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_ach_seq_number_old_idx ON public.merchant_aches USING btree (ach_seq_number_old);


--
-- Name: merchant_achs_billing_option_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_billing_option_id_old_idx ON public.merchant_aches USING btree (billing_option_id_old);


--
-- Name: merchant_achs_merchant_ach_app_status_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_merchant_ach_app_status_id_idx ON public.merchant_aches USING btree (merchant_ach_app_status_id);


--
-- Name: merchant_achs_merchant_ach_billing_option_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_merchant_ach_billing_option_id_idx ON public.merchant_aches USING btree (merchant_ach_billing_option_id);


--
-- Name: merchant_achs_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_merchant_id_idx ON public.merchant_aches USING btree (merchant_id);


--
-- Name: merchant_achs_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_merchant_id_old_idx ON public.merchant_aches USING btree (merchant_id_old);


--
-- Name: merchant_achs_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_user_id_idx ON public.merchant_aches USING btree (user_id);


--
-- Name: merchant_achs_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_achs_user_id_old_idx ON public.merchant_aches USING btree (user_id_old);


--
-- Name: merchant_acquirers_acquirer_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_acquirers_acquirer_id_old_idx ON public.merchant_acquirers USING btree (acquirer_id_old);


--
-- Name: merchant_active; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_active ON public.merchants USING btree (active);


--
-- Name: merchant_app_status_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_app_status_id_old_idx ON public.merchant_aches USING btree (app_status_id_old);


--
-- Name: merchant_banks_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_banks_merchant_id_idx ON public.merchant_banks USING btree (merchant_id);


--
-- Name: merchant_banks_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_banks_merchant_id_old_idx ON public.merchant_banks USING btree (merchant_id_old);


--
-- Name: merchant_cancellation_fee_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_fee_id_idx ON public.merchants USING btree (cancellation_fee_id);


--
-- Name: merchant_cancellation_fee_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_fee_id_old_idx ON public.merchants USING btree (cancellation_fee_id_old);


--
-- Name: merchant_cancellation_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_index1 ON public.merchant_cancellations USING btree (date_submitted);


--
-- Name: merchant_cancellation_index2; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_index2 ON public.merchant_cancellations USING btree (date_completed);


--
-- Name: merchant_cancellation_index3; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_index3 ON public.merchant_cancellations USING btree (status);


--
-- Name: merchant_cancellation_index9; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_index9 ON public.merchant_cancellations USING btree (date_inactive);


--
-- Name: merchant_cancellation_subreason_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_subreason_id_old_idx ON public.merchant_cancellations USING btree (subreason_id_old);


--
-- Name: merchant_cancellation_subreason_merchant_cancellation_subreason; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellation_subreason_merchant_cancellation_subreason ON public.merchant_cancellations USING btree (merchant_cancellation_subreason_id);


--
-- Name: merchant_cancellations_history_merchant_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellations_history_merchant_id ON public.merchant_cancellations_histories USING btree (merchant_id);


--
-- Name: merchant_cancellations_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellations_merchant_id_idx ON public.merchant_cancellations USING btree (merchant_id);


--
-- Name: merchant_cancellations_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_cancellations_merchant_id_old_idx ON public.merchant_cancellations USING btree (merchant_id_old);


--
-- Name: merchant_card_types_card_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_card_types_card_type_id_idx ON public.merchant_card_types USING btree (card_type_id);


--
-- Name: merchant_card_types_card_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_card_types_card_type_old_idx ON public.merchant_card_types USING btree (card_type_old);


--
-- Name: merchant_card_types_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_card_types_merchant_id_idx ON public.merchant_card_types USING btree (merchant_id);


--
-- Name: merchant_card_types_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_card_types_merchant_id_old_idx ON public.merchant_card_types USING btree (merchant_id_old);


--
-- Name: merchant_changes_approved_by_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_approved_by_user_id_idx ON public.merchant_changes USING btree (approved_by_user_id);


--
-- Name: merchant_changes_change_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_change_id_old_idx ON public.merchant_changes USING btree (change_id_old);


--
-- Name: merchant_changes_change_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_change_type_id_idx ON public.merchant_changes USING btree (change_type_id);


--
-- Name: merchant_changes_change_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_change_type_old_idx ON public.merchant_changes USING btree (change_type_old);


--
-- Name: merchant_changes_equipment_programming_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_equipment_programming_id_idx ON public.merchant_changes USING btree (equipment_programming_id);


--
-- Name: merchant_changes_merchant_approved_by_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_merchant_approved_by_user_id_old_idx ON public.merchant_changes USING btree (approved_by_old);


--
-- Name: merchant_changes_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_merchant_id_idx ON public.merchant_changes USING btree (merchant_id);


--
-- Name: merchant_changes_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_merchant_id_old_idx ON public.merchant_changes USING btree (merchant_id_old);


--
-- Name: merchant_changes_merchant_note_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_merchant_note_id_idx ON public.merchant_changes USING btree (merchant_note_id);


--
-- Name: merchant_changes_merchant_note_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_merchant_note_id_old_idx ON public.merchant_changes USING btree (merchant_note_id_old);


--
-- Name: merchant_changes_programming_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_programming_id_old_idx ON public.merchant_changes USING btree (programming_id_old);


--
-- Name: merchant_changes_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_user_id_idx ON public.merchant_changes USING btree (user_id);


--
-- Name: merchant_changes_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_changes_user_id_old_idx ON public.merchant_changes USING btree (user_id_old);


--
-- Name: merchant_dba_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_dba_idx ON public.merchants USING btree (merchant_dba);


--
-- Name: merchant_dbalower_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_dbalower_idx ON public.merchants USING btree (merchant_dba);


--
-- Name: merchant_entity_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_entity_id_idx ON public.merchants USING btree (entity_id);


--
-- Name: merchant_entity_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_entity_old_idx ON public.merchants USING btree (entity_old);


--
-- Name: merchant_gateways_archives_gateways_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_gateways_archives_gateways_index ON public.merchant_gateways_archives USING btree (gateway_id);


--
-- Name: merchant_gateways_archives_merchants_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_gateways_archives_merchants_index ON public.merchant_gateways_archives USING btree (merchant_id);


--
-- Name: merchant_gateways_archives_user_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_gateways_archives_user_index ON public.merchant_gateways_archives USING btree (user_id);


--
-- Name: merchant_group_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_group_id_idx ON public.merchants USING btree (group_id);


--
-- Name: merchant_group_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_group_id_old_idx ON public.merchants USING btree (group_id_old);


--
-- Name: merchant_id_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX merchant_id_key ON public.onlineapp_multipasses USING btree (merchant_id);


--
-- Name: merchant_merchant_acquirer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_merchant_acquirer_id_idx ON public.merchants USING btree (merchant_acquirer_id);


--
-- Name: merchant_merchant_acquirer_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_merchant_acquirer_id_old_idx ON public.merchants USING btree (merchant_acquirer_id_old);


--
-- Name: merchant_merchant_bin_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_merchant_bin_id_idx ON public.merchants USING btree (merchant_bin_id);


--
-- Name: merchant_merchant_bin_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_merchant_bin_id_old_idx ON public.merchants USING btree (merchant_bin_id_old);


--
-- Name: merchant_network_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_network_id_idx ON public.merchants USING btree (network_id);


--
-- Name: merchant_network_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_network_id_old_idx ON public.merchants USING btree (network_id_old);


--
-- Name: merchant_note_merchant_note_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_note_merchant_note_id_old_idx ON public.merchant_notes USING btree (merchant_note_id_old);


--
-- Name: merchant_notes_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_notes_merchant_id_idx ON public.merchant_notes USING btree (merchant_id);


--
-- Name: merchant_notes_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_notes_merchant_id_old_idx ON public.merchant_notes USING btree (merchant_id_old);


--
-- Name: merchant_notes_note_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_notes_note_type_id_idx ON public.merchant_notes USING btree (note_type_id);


--
-- Name: merchant_notes_note_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_notes_note_type_id_old_idx ON public.merchant_notes USING btree (note_type_id_old);


--
-- Name: merchant_notes_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_notes_user_id_idx ON public.merchant_notes USING btree (user_id);


--
-- Name: merchant_notes_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_notes_user_id_old_idx ON public.merchant_notes USING btree (user_id_old);


--
-- Name: merchant_owner_owner_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_owner_owner_id_old_idx ON public.merchant_owners USING btree (owner_id_old);


--
-- Name: merchant_owners_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_owners_merchant_id_idx ON public.merchant_owners USING btree (merchant_id);


--
-- Name: merchant_owners_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_owners_merchant_id_old_idx ON public.merchant_owners USING btree (merchant_id_old);


--
-- Name: merchant_partner_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_partner_id_idx ON public.merchants USING btree (partner_id);


--
-- Name: merchant_partner_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_partner_id_old_idx ON public.merchants USING btree (partner_id_old);


--
-- Name: merchant_pcis_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_pcis_merchant_id_idx ON public.merchant_pcis USING btree (merchant_id);


--
-- Name: merchant_pcis_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_pcis_merchant_id_old_idx ON public.merchant_pcis USING btree (merchant_id_old);


--
-- Name: merchant_pricing_archive_merchant_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_pricing_archive_merchant_index ON public.merchant_pricing_archives USING btree (merchant_id);


--
-- Name: merchant_pricing_archive_month_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_pricing_archive_month_index ON public.merchant_pricing_archives USING btree (month);


--
-- Name: merchant_pricing_archive_products_services_type_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_pricing_archive_products_services_type_id_index ON public.merchant_pricing_archives USING btree (products_services_type_id);


--
-- Name: merchant_pricing_archive_user_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_pricing_archive_user_index ON public.merchant_pricing_archives USING btree (user_id);


--
-- Name: merchant_pricing_archive_year_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_pricing_archive_year_index ON public.merchant_pricing_archives USING btree (year);


--
-- Name: merchant_reference_merchant_ref_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_reference_merchant_ref_type_old_idx ON public.merchant_references USING btree (merchant_ref_type_old);


--
-- Name: merchant_reference_merchant_reference_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_reference_merchant_reference_type_id_idx ON public.merchant_references USING btree (merchant_reference_type_id);


--
-- Name: merchant_references_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_references_merchant_id_idx ON public.merchant_references USING btree (merchant_id);


--
-- Name: merchant_references_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_references_merchant_id_old_idx ON public.merchant_references USING btree (merchant_id_old);


--
-- Name: merchant_referer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_referer_id_idx ON public.merchants USING btree (referer_id);


--
-- Name: merchant_referer_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_referer_id_old_idx ON public.merchants USING btree (ref_seq_number_old);


--
-- Name: merchant_rejects_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_rejects_merchant_id_idx ON public.merchant_rejects USING btree (merchant_id);


--
-- Name: merchant_rejects_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_rejects_merchant_id_old_idx ON public.merchant_rejects USING btree (merchant_id_old);


--
-- Name: merchant_rejects_merchant_reject_recurrance_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_rejects_merchant_reject_recurrance_id_idx ON public.merchant_rejects USING btree (merchant_reject_recurrance_id);


--
-- Name: merchant_rejects_merchant_reject_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_rejects_merchant_reject_type_id_idx ON public.merchant_rejects USING btree (merchant_reject_type_id);


--
-- Name: merchant_reseller_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_reseller_id_idx ON public.merchants USING btree (reseller_id);


--
-- Name: merchant_reseller_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_reseller_id_old_idx ON public.merchants USING btree (res_seq_number_old);


--
-- Name: merchant_type_unique; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX merchant_type_unique ON public.merchant_types USING btree (type_description);


--
-- Name: merchant_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_user_id_idx ON public.merchants USING btree (user_id);


--
-- Name: merchant_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_user_id_old_idx ON public.merchants USING btree (user_id_old);


--
-- Name: merchant_uw_expedited_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_expedited_index ON public.merchant_uws USING btree (expedited);


--
-- Name: merchant_uw_final_approved_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_final_approved_id_old_index ON public.merchant_uws USING btree (final_approved_id_old);


--
-- Name: merchant_uw_final_date_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_final_date_index ON public.merchant_uws USING btree (final_date);


--
-- Name: merchant_uw_final_status_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_final_status_id_old_index ON public.merchant_uws USING btree (final_status_id_old);


--
-- Name: merchant_uw_merchant_uw_final_approved_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_merchant_uw_final_approved_id_index ON public.merchant_uws USING btree (merchant_uw_final_approved_id);


--
-- Name: merchant_uw_merchant_uw_final_status_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_merchant_uw_final_status_id_index ON public.merchant_uws USING btree (merchant_uw_final_status_id);


--
-- Name: merchant_uw_tier_assignment_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_tier_assignment_index ON public.merchant_uws USING btree (tier_assignment);


--
-- Name: merchant_uw_volumes_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uw_volumes_merchant_id_idx ON public.merchant_uw_volumes USING btree (merchant_id);


--
-- Name: merchant_uws_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uws_merchant_id_idx ON public.merchant_uws USING btree (merchant_id);


--
-- Name: merchant_uws_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX merchant_uws_merchant_id_old_idx ON public.merchant_uws USING btree (merchant_id_old);


--
-- Name: merchants_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX merchants_merchant_id_old_idx ON public.merchants USING btree (merchant_id_old);


--
-- Name: model_foreign_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX model_foreign_key ON public.loggable_logs USING btree (foreign_key, model);


--
-- Name: modified_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX modified_idx ON public.onlineapp_applications USING btree (modified);


--
-- Name: mp_compliance_fee; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mp_compliance_fee ON public.merchant_pcis USING btree (compliance_fee);


--
-- Name: mp_insurance_fee; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mp_insurance_fee ON public.merchant_pcis USING btree (insurance_fee);


--
-- Name: mp_saq_completed_date; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mp_saq_completed_date ON public.merchant_pcis USING btree (saq_completed_date);


--
-- Name: mpid_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mpid_index1 ON public.merchants USING btree (partner_id);


--
-- Name: mr_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mr_id_old_idx ON public.merchant_reject_types USING btree (id_old);


--
-- Name: mr_open_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mr_open_index1 ON public.merchant_rejects USING btree (open);


--
-- Name: mr_recurrance_id_old_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mr_recurrance_id_old_index1 ON public.merchant_rejects USING btree (recurrance_id_old);


--
-- Name: mr_reject_date_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mr_reject_date_index1 ON public.merchant_rejects USING btree (reject_date);


--
-- Name: mr_status_date_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mr_status_date_index1 ON public.merchant_reject_lines USING btree (status_date);


--
-- Name: mr_trace_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX mr_trace_index1 ON public.merchant_rejects USING btree (trace);


--
-- Name: mr_type_id_oldindex1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mr_type_id_oldindex1 ON public.merchant_rejects USING btree (type_id_old);


--
-- Name: mrl_merchant_reject_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrl_merchant_reject_id_idx ON public.merchant_reject_lines USING btree (merchant_reject_id);


--
-- Name: mrl_merchant_reject_status_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrl_merchant_reject_status_id_idx ON public.merchant_reject_lines USING btree (merchant_reject_status_id);


--
-- Name: mrl_reject_id_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrl_reject_id_index1 ON public.merchant_reject_lines USING btree (reject_id_old);


--
-- Name: mrl_status_id_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrl_status_id_index1 ON public.merchant_reject_lines USING btree (status_id_old);


--
-- Name: mrr_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrr_id_old_idx ON public.merchant_reject_recurrances USING btree (id_old);


--
-- Name: mrs_collected_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrs_collected_index1 ON public.merchant_reject_statuses USING btree (collected);


--
-- Name: mrs_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrs_id_old_idx ON public.merchant_reject_statuses USING btree (id_old);


--
-- Name: mrt_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mrt_id_old_idx ON public.merchant_reject_types USING btree (id_old);


--
-- Name: mufa_merchant_uw_final_approved_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mufa_merchant_uw_final_approved_id_old_idx ON public.merchant_uw_final_approveds USING btree (merchant_uw_final_approved_id_old);


--
-- Name: mufs_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX mufs_id_old_idx ON public.merchant_uw_final_statuses USING btree (id_old);


--
-- Name: oca_template_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX oca_template_id_idx ON public.onlineapp_cobranded_applications USING btree (template_id);


--
-- Name: oca_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX oca_user_id_idx ON public.onlineapp_cobranded_applications USING btree (user_id);


--
-- Name: oet_app_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX oet_app_id_idx ON public.onlineapp_email_timelines USING btree (app_id);


--
-- Name: onlineapp_api_logs_user_id_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX onlineapp_api_logs_user_id_key ON public.onlineapp_api_logs USING btree (user_id);


--
-- Name: orderitems_eqtype; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_eqtype ON public.orderitems USING btree (equipment_type_id);


--
-- Name: orderitems_itemmerchid; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_itemmerchid ON public.orderitems USING btree (item_merchant_id);


--
-- Name: orderitems_order_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_order_idx ON public.orderitems USING btree (order_id);


--
-- Name: orderitems_orderitem_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_orderitem_type_id_old_idx ON public.orderitem_types USING btree (orderitem_type_id_old);


--
-- Name: orderitems_replacement_shipping_axia_to_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_replacement_shipping_axia_to_merchant_id_idx ON public.orderitems_replacements USING btree (shipping_axia_to_merchant_id);


--
-- Name: orderitems_replacement_shipping_merchant_to_vendor_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_replacement_shipping_merchant_to_vendor_id_idx ON public.orderitems_replacements USING btree (shipping_merchant_to_vendor_id);


--
-- Name: orderitems_replacement_shipping_vendor_to_axia_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_replacement_shipping_vendor_to_axia_id_idx ON public.orderitems_replacements USING btree (shipping_vendor_to_axia_id);


--
-- Name: orderitems_type_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orderitems_type_id ON public.orderitems USING btree (type_id);


--
-- Name: orders_ach_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_ach_seq_number_old_idx ON public.orders USING btree (ach_seq_number_old);


--
-- Name: orders_commissionmonth; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_commissionmonth ON public.orders USING btree (depricated_commission_month);


--
-- Name: orders_invnum; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_invnum ON public.orders USING btree (invoice_number);


--
-- Name: orders_merchant_ach_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_merchant_ach_id_idx ON public.orders USING btree (merchant_ach_id);


--
-- Name: orders_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_merchant_id_idx ON public.orders USING btree (merchant_id);


--
-- Name: orders_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_merchant_id_old_idx ON public.orders USING btree (merchant_id_old);


--
-- Name: orders_order_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_order_id_old_idx ON public.orders USING btree (order_id_old);


--
-- Name: orders_shipping_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_shipping_type_id_idx ON public.orders USING btree (shipping_type_id);


--
-- Name: orders_shiptype; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_shiptype ON public.orders USING btree (shipping_type_old);


--
-- Name: orders_status; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_status ON public.orders USING btree (status);


--
-- Name: orders_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_user_id_idx ON public.orders USING btree (user_id);


--
-- Name: orders_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_user_id_old_idx ON public.orders USING btree (user_id_old);


--
-- Name: orders_vendor_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_vendor_id_idx ON public.orders USING btree (vendor_id);


--
-- Name: orders_vendor_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX orders_vendor_id_old_idx ON public.orders USING btree (vendor_id_old);


--
-- Name: ot_cobrand_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ot_cobrand_id_idx ON public.onlineapp_templates USING btree (cobrand_id);


--
-- Name: ot_template_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ot_template_id_idx ON public.onlineapp_users USING btree (template_id);


--
-- Name: otp_template_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX otp_template_id_idx ON public.onlineapp_template_pages USING btree (template_id);


--
-- Name: oum_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX oum_id_idx ON public.onlineapp_users_managers USING btree (id);


--
-- Name: oum_manager_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX oum_manager_id_idx ON public.onlineapp_users_managers USING btree (manager_id);


--
-- Name: oum_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX oum_user_id_idx ON public.onlineapp_users_managers USING btree (user_id);


--
-- Name: owner1_fullname_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX owner1_fullname_idx ON public.onlineapp_applications USING btree (owner1_fullname);


--
-- Name: owner2_fullname_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX owner2_fullname_idx ON public.onlineapp_applications USING btree (owner2_fullname);


--
-- Name: pactive_index1; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pactive_index1 ON public.partners USING btree (active);


--
-- Name: page_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX page_id_idx ON public.onlineapp_template_sections USING btree (page_id);


--
-- Name: parent_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX parent_id ON public.tickler_comments USING btree (parent_id);


--
-- Name: parent_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX parent_idx ON public.roles USING btree (parent_id);


--
-- Name: pci_compliance_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_id_old_idx ON public.pci_compliance_date_types USING btree (id_old);


--
-- Name: pci_compliance_pci_compliance_date_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_pci_compliance_date_type_id_idx ON public.pci_compliances USING btree (pci_compliance_date_type_id);


--
-- Name: pci_compliance_pci_compliance_date_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_pci_compliance_date_type_id_old_idx ON public.pci_compliances USING btree (pci_compliance_date_type_id_old);


--
-- Name: pci_compliance_saq_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_saq_merchant_id_idx ON public.pci_compliances USING btree (saq_merchant_id);


--
-- Name: pci_compliance_status_log_pci_compliance_date_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_status_log_pci_compliance_date_type_id_idx ON public.pci_compliance_status_logs USING btree (pci_compliance_date_type_id);


--
-- Name: pci_compliance_status_log_pci_compliance_date_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_status_log_pci_compliance_date_type_id_old_idx ON public.pci_compliance_status_logs USING btree (pci_compliance_date_type_id_old);


--
-- Name: pci_compliance_status_log_saq_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_status_log_saq_merchant_id_idx ON public.pci_compliance_status_logs USING btree (saq_merchant_id);


--
-- Name: pci_compliance_status_logsaq_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliance_status_logsaq_merchant_id_old_idx ON public.pci_compliance_status_logs USING btree (saq_merchant_id_old);


--
-- Name: pci_compliancesaq_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pci_compliancesaq_merchant_id_old_idx ON public.pci_compliances USING btree (saq_merchant_id_old);


--
-- Name: pk_group_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pk_group_id ON public.permissions_roles USING btree (role_id, permission_id);


--
-- Name: pk_roles_users; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pk_roles_users ON public.users_roles USING btree (role_id, user_id);


--
-- Name: pricing_matrix_matrix_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pricing_matrix_matrix_id_old_idx ON public.pricing_matrices USING btree (matrix_id_old);


--
-- Name: pricing_matrix_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pricing_matrix_user_id_idx ON public.pricing_matrices USING btree (user_id);


--
-- Name: pricing_matrix_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pricing_matrix_user_id_old_idx ON public.pricing_matrices USING btree (user_id_old);


--
-- Name: pricing_matrix_user_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pricing_matrix_user_type_id_idx ON public.pricing_matrices USING btree (user_type_id);


--
-- Name: pricing_matrix_user_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX pricing_matrix_user_type_old_idx ON public.pricing_matrices USING btree (user_type_old);


--
-- Name: product_service_type_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX product_service_type_index ON public.residual_product_controls USING btree (product_service_type_id);


--
-- Name: products_and_services_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX products_and_services_merchant_id_idx ON public.products_and_services USING btree (merchant_id);


--
-- Name: products_and_services_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX products_and_services_merchant_id_old_idx ON public.products_and_services USING btree (merchant_id_old);


--
-- Name: products_and_services_products_services_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX products_and_services_products_services_type_id_idx ON public.products_and_services USING btree (products_services_type_id);


--
-- Name: products_and_services_products_services_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX products_and_services_products_services_type_id_old_idx ON public.products_and_services USING btree (products_services_type_id_old);


--
-- Name: products_services_types_products_services_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX products_services_types_products_services_type_id_old_idx ON public.products_services_types USING btree (products_services_type_id_old);


--
-- Name: profit_projections_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX profit_projections_merchant_id_idx ON public.profit_projections USING btree (merchant_id);


--
-- Name: profit_projections_product_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX profit_projections_product_id_idx ON public.profit_projections USING btree (products_services_type_id);


--
-- Name: profitability_report_merchant_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX profitability_report_merchant_id_index ON public.profitability_reports USING btree (merchant_id);


--
-- Name: r_prod_settings_product_services_type_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX r_prod_settings_product_services_type_index ON public.rep_product_settings USING btree (products_services_type_id);


--
-- Name: r_prod_settings_ucp_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX r_prod_settings_ucp_id_index ON public.rep_product_settings USING btree (user_compensation_profile_id);


--
-- Name: referer_products_services_xrefs_products_services_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referer_products_services_xrefs_products_services_type_id_idx ON public.referer_products_services_xrefs USING btree (products_services_type_id);


--
-- Name: referer_products_services_xrefs_products_services_type_id_old_i; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referer_products_services_xrefs_products_services_type_id_old_i ON public.referer_products_services_xrefs USING btree (products_services_type_id_old);


--
-- Name: referer_products_services_xrefs_ref_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referer_products_services_xrefs_ref_seq_number_old_idx ON public.referer_products_services_xrefs USING btree (ref_seq_number_old);


--
-- Name: referer_products_services_xrefs_referer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referer_products_services_xrefs_referer_id_idx ON public.referer_products_services_xrefs USING btree (referer_id);


--
-- Name: referers_active; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referers_active ON public.referers USING btree (active);


--
-- Name: referers_bet_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referers_bet_bet_code_old_idx ON public.referers_bets USING btree (bet_code_old);


--
-- Name: referers_bet_bet_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referers_bet_bet_id_idx ON public.referers_bets USING btree (bet_id);


--
-- Name: referers_bet_ref_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referers_bet_ref_seq_number_old_idx ON public.referers_bets USING btree (ref_seq_number_old);


--
-- Name: referers_bet_referer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referers_bet_referer_id_idx ON public.referers_bets USING btree (referer_id);


--
-- Name: referers_bet_referers_bet_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referers_bet_referers_bet_id_old_idx ON public.referers_bets USING btree (referers_bet_id_old);


--
-- Name: referers_ref_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX referers_ref_seq_number_old_idx ON public.referers USING btree (ref_seq_number_old);


--
-- Name: rep_cost_structures_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_cost_structures_user_id_idx ON public.rep_cost_structures USING btree (user_id);


--
-- Name: rep_cost_structures_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_cost_structures_user_id_old_idx ON public.rep_cost_structures USING btree (user_id_old);


--
-- Name: rep_gateway_cost_user_comp_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_gateway_cost_user_comp_index ON public.gateway_cost_structures USING btree (user_compensation_profile_id);


--
-- Name: rep_monthly_cost_user_comp_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_monthly_cost_user_comp_index ON public.rep_monthly_costs USING btree (user_compensation_profile_id);


--
-- Name: rep_product_profit_pcts_products_services_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_product_profit_pcts_products_services_type_id_idx ON public.rep_product_profit_pcts USING btree (products_services_type_id);


--
-- Name: rep_product_profit_pcts_products_services_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_product_profit_pcts_products_services_type_id_old_idx ON public.rep_product_profit_pcts USING btree (products_services_type_id_old);


--
-- Name: rep_product_profit_pcts_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_product_profit_pcts_user_id_idx ON public.rep_product_profit_pcts USING btree (user_id);


--
-- Name: rep_product_profit_pcts_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rep_product_profit_pcts_user_id_old_idx ON public.rep_product_profit_pcts USING btree (user_id_old);


--
-- Name: residual_pricing_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_bet_code_old_idx ON public.residual_pricings USING btree (bet_code_old);


--
-- Name: residual_pricing_bet_table_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_bet_table_id_idx ON public.residual_pricings USING btree (bet_table_id);


--
-- Name: residual_pricing_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_merchant_id_idx ON public.residual_pricings USING btree (merchant_id);


--
-- Name: residual_pricing_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_merchant_id_old_idx ON public.residual_pricings USING btree (merchant_id_old);


--
-- Name: residual_pricing_month; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_month ON public.residual_pricings USING btree (r_month);


--
-- Name: residual_pricing_network; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_network ON public.residual_pricings USING btree (r_network);


--
-- Name: residual_pricing_products_services_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_products_services_type_id_idx ON public.residual_pricings USING btree (products_services_type_id);


--
-- Name: residual_pricing_products_services_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_products_services_type_id_old_idx ON public.residual_pricings USING btree (products_services_type_id_old);


--
-- Name: residual_pricing_ref_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_ref_seq_number_old_idx ON public.residual_pricings USING btree (ref_seq_number_old);


--
-- Name: residual_pricing_referer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_referer_id_idx ON public.residual_pricings USING btree (referer_id);


--
-- Name: residual_pricing_res_seq_number_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_res_seq_number_old_idx ON public.residual_pricings USING btree (res_seq_number_old);


--
-- Name: residual_pricing_reseller_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_reseller_id_idx ON public.residual_pricings USING btree (reseller_id);


--
-- Name: residual_pricing_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_user_id_idx ON public.residual_pricings USING btree (user_id);


--
-- Name: residual_pricing_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_user_id_old_idx ON public.residual_pricings USING btree (user_id_old);


--
-- Name: residual_pricing_year; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_pricing_year ON public.residual_pricings USING btree (r_year);


--
-- Name: residual_report_end_date_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_end_date_idx ON public.residual_reports USING btree (public.make_date((r_year)::integer, (r_month)::integer, 28));


--
-- Name: residual_report_month; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_month ON public.residual_reports USING btree (r_month);


--
-- Name: residual_report_products_services_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_products_services_type_id_idx ON public.residual_reports USING btree (products_services_type_id);


--
-- Name: residual_report_products_services_type_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_products_services_type_id_old_idx ON public.residual_reports USING btree (products_services_type_id_old);


--
-- Name: residual_report_referer_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_referer_id_idx ON public.residual_reports USING btree (referer_id);


--
-- Name: residual_report_res_seqnum; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_res_seqnum ON public.residual_reports USING btree (res_seq_number_old);


--
-- Name: residual_report_reseller_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_reseller_id_idx ON public.residual_reports USING btree (reseller_id);


--
-- Name: residual_report_seqnum; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_seqnum ON public.residual_reports USING btree (ref_seq_number_old);


--
-- Name: residual_report_start_date_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_start_date_idx ON public.residual_reports USING btree (public.make_date((r_year)::integer, (r_month)::integer, 1));


--
-- Name: residual_report_status; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_status ON public.residual_reports USING btree (status);


--
-- Name: residual_report_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_user_id_idx ON public.residual_reports USING btree (user_id);


--
-- Name: residual_report_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_user_id_old_idx ON public.residual_reports USING btree (user_id_old);


--
-- Name: residual_report_year; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_report_year ON public.residual_reports USING btree (r_year);


--
-- Name: residual_reports_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_reports_merchant_id_idx ON public.residual_reports USING btree (merchant_id);


--
-- Name: residual_reports_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_reports_merchant_id_old_idx ON public.residual_reports USING btree (merchant_id_old);


--
-- Name: residual_time_parameters_associated_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_time_parameters_associated_user_id_idx ON public.residual_time_parameters USING btree (associated_user_id);


--
-- Name: residual_time_parameters_products_services_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_time_parameters_products_services_type_id_idx ON public.residual_time_parameters USING btree (products_services_type_id);


--
-- Name: residual_time_parameters_residual_parameter_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX residual_time_parameters_residual_parameter_type_id_idx ON public.residual_time_parameters USING btree (residual_parameter_type_id);


--
-- Name: rght; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rght ON public.tickler_comments USING btree (rght);


--
-- Name: rpxref_mgr1_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_mgr1_id_idx ON public.rep_partner_xrefs USING btree (mgr1_id);


--
-- Name: rpxref_mgr1_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_mgr1_id_old_idx ON public.rep_partner_xrefs USING btree (mgr1_id_old);


--
-- Name: rpxref_mgr2_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_mgr2_id_idx ON public.rep_partner_xrefs USING btree (mgr2_id);


--
-- Name: rpxref_mgr2_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_mgr2_id_old_idx ON public.rep_partner_xrefs USING btree (mgr2_id_old);


--
-- Name: rpxref_partner_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_partner_id_idx ON public.rep_partner_xrefs USING btree (partner_id);


--
-- Name: rpxref_partner_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_partner_id_old_idx ON public.rep_partner_xrefs USING btree (partner_id_old);


--
-- Name: rpxref_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_user_id_idx ON public.rep_partner_xrefs USING btree (user_id);


--
-- Name: rpxref_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rpxref_user_id_old_idx ON public.rep_partner_xrefs USING btree (user_id_old);


--
-- Name: rr_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_bet_code_old_idx ON public.residual_reports USING btree (bet_code_old);


--
-- Name: rr_bet_table_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_bet_table_id_idx ON public.residual_reports USING btree (bet_table_id);


--
-- Name: rr_manager_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_manager_id_idx ON public.residual_reports USING btree (manager_id);


--
-- Name: rr_manager_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_manager_id_old_idx ON public.residual_reports USING btree (manager_id_old);


--
-- Name: rr_manager_id_secondary; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_manager_id_secondary ON public.residual_reports USING btree (manager_id_secondary);


--
-- Name: rr_manager_id_secondary_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_manager_id_secondary_old_idx ON public.residual_reports USING btree (manager_id_secondary_old);


--
-- Name: rr_partner_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_partner_id_idx ON public.residual_reports USING btree (partner_id);


--
-- Name: rr_partner_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rr_partner_id_old_idx ON public.residual_reports USING btree (partner_id_old);


--
-- Name: rs_document_guid_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX rs_document_guid_idx ON public.onlineapp_applications USING btree (rs_document_guid);


--
-- Name: sa_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sa_id_old_idx ON public.saq_answers USING btree (id_old);


--
-- Name: sa_saq_merchant_survey_xref_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sa_saq_merchant_survey_xref_id_idx ON public.saq_answers USING btree (saq_merchant_survey_xref_id);


--
-- Name: sa_saq_merchant_survey_xref_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sa_saq_merchant_survey_xref_id_old_idx ON public.saq_answers USING btree (saq_merchant_survey_xref_id_old);


--
-- Name: sa_saq_survey_question_xref_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sa_saq_survey_question_xref_id_idx ON public.saq_answers USING btree (saq_survey_question_xref_id);


--
-- Name: sa_saq_survey_question_xref_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sa_saq_survey_question_xref_id_old_idx ON public.saq_answers USING btree (saq_survey_question_xref_id_old);


--
-- Name: sales_goal_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sales_goal_user_id_idx ON public.sales_goals USING btree (user_id);


--
-- Name: sales_goal_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sales_goal_user_id_old_idx ON public.sales_goals USING btree (user_id_old);


--
-- Name: saq_control_scan_unboardeds_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX saq_control_scan_unboardeds_merchant_id_idx ON public.saq_control_scan_unboardeds USING btree (merchant_id);


--
-- Name: saq_control_scan_unboardeds_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX saq_control_scan_unboardeds_merchant_id_old_idx ON public.saq_control_scan_unboardeds USING btree (merchant_id_old);


--
-- Name: saq_control_scans_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX saq_control_scans_merchant_id_idx ON public.saq_control_scans USING btree (merchant_id);


--
-- Name: saq_control_scans_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX saq_control_scans_merchant_id_old_idx ON public.saq_control_scans USING btree (merchant_id_old);


--
-- Name: saq_merchantq_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX saq_merchantq_merchant_id_old_idx ON public.saq_merchants USING btree (merchant_id_old);


--
-- Name: saq_merchants_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX saq_merchants_id_old_idx ON public.saq_merchants USING btree (id_old);


--
-- Name: saq_merchants_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX saq_merchants_merchant_id_idx ON public.saq_merchants USING btree (merchant_id);


--
-- Name: scs_creation_date; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_creation_date ON public.saq_control_scans USING btree (creation_date);


--
-- Name: scs_first_questionnaire_date; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_first_questionnaire_date ON public.saq_control_scans USING btree (first_questionnaire_date);


--
-- Name: scs_first_scan_date; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_first_scan_date ON public.saq_control_scans USING btree (first_scan_date);


--
-- Name: scs_pci_compliance; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_pci_compliance ON public.saq_control_scans USING btree (pci_compliance);


--
-- Name: scs_quarterly_scan_fee; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_quarterly_scan_fee ON public.saq_control_scans USING btree (quarterly_scan_fee);


--
-- Name: scs_questionnaire_status; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_questionnaire_status ON public.saq_control_scans USING btree (questionnaire_status);


--
-- Name: scs_saq_type; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_saq_type ON public.saq_control_scans USING btree (saq_type);


--
-- Name: scs_scan_status; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_scan_status ON public.saq_control_scans USING btree (scan_status);


--
-- Name: scs_sua; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scs_sua ON public.saq_control_scans USING btree (sua);


--
-- Name: scsu_date_unboarded; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scsu_date_unboarded ON public.saq_control_scan_unboardeds USING btree (date_unboarded);


--
-- Name: scsu_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX scsu_id_old_idx ON public.saq_control_scan_unboardeds USING btree (id_old);


--
-- Name: sg_archive_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX sg_archive_user_id_idx ON public.sales_goal_archives USING btree (user_id, goal_month, goal_year);


--
-- Name: sg_archive_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX sg_archive_user_id_old_idx ON public.sales_goal_archives USING btree (user_id_old, goal_month, goal_year);


--
-- Name: shipping_types_shipping_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX shipping_types_shipping_type_old_idx ON public.shipping_types USING btree (shipping_type_old);


--
-- Name: sm_billing_date; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sm_billing_date ON public.saq_merchants USING btree (billing_date);


--
-- Name: sm_email_sent; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sm_email_sent ON public.saq_merchants USING btree (email_sent);


--
-- Name: sm_merchant_email; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sm_merchant_email ON public.saq_merchants USING btree (merchant_email);


--
-- Name: sm_merchant_name; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sm_merchant_name ON public.saq_merchants USING btree (merchant_name);


--
-- Name: sm_next_billing_date; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sm_next_billing_date ON public.saq_merchants USING btree (next_billing_date);


--
-- Name: sm_password; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sm_password ON public.saq_merchants USING btree (password);


--
-- Name: smpe_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smpe_id_old_idx ON public.saq_merchant_pci_emails USING btree (id_old);


--
-- Name: smpes_date_sent; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smpes_date_sent ON public.saq_merchant_pci_email_sents USING btree (date_sent);


--
-- Name: smpes_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smpes_id_old_idx ON public.saq_merchant_pci_email_sents USING btree (id_old);


--
-- Name: smpes_saq_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smpes_saq_merchant_id_idx ON public.saq_merchant_pci_email_sents USING btree (saq_merchant_id);


--
-- Name: smpes_saq_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smpes_saq_merchant_id_old_idx ON public.saq_merchant_pci_email_sents USING btree (saq_merchant_id_old);


--
-- Name: smpes_saq_merchant_pci_email_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smpes_saq_merchant_pci_email_id_idx ON public.saq_merchant_pci_email_sents USING btree (saq_merchant_pci_email_id);


--
-- Name: smpes_saq_merchant_pci_email_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smpes_saq_merchant_pci_email_id_old_idx ON public.saq_merchant_pci_email_sents USING btree (saq_merchant_pci_email_id_old);


--
-- Name: smsx_acknowledgement_name; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_acknowledgement_name ON public.saq_merchant_survey_xrefs USING btree (acknowledgement_name);


--
-- Name: smsx_datecomplete; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_datecomplete ON public.saq_merchant_survey_xrefs USING btree (datecomplete);


--
-- Name: smsx_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_id_old_idx ON public.saq_merchant_survey_xrefs USING btree (id_old);


--
-- Name: smsx_saq_confirmation_survey_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_confirmation_survey_id_idx ON public.saq_merchant_survey_xrefs USING btree (saq_confirmation_survey_id);


--
-- Name: smsx_saq_confirmation_survey_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_confirmation_survey_id_old_idx ON public.saq_merchant_survey_xrefs USING btree (saq_confirmation_survey_id_old);


--
-- Name: smsx_saq_eligibility_survey_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_eligibility_survey_id_idx ON public.saq_merchant_survey_xrefs USING btree (saq_eligibility_survey_id);


--
-- Name: smsx_saq_eligibility_survey_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_eligibility_survey_id_old_idx ON public.saq_merchant_survey_xrefs USING btree (saq_eligibility_survey_id_old);


--
-- Name: smsx_saq_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_merchant_id_idx ON public.saq_merchant_survey_xrefs USING btree (saq_merchant_id);


--
-- Name: smsx_saq_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_merchant_id_old_idx ON public.saq_merchant_survey_xrefs USING btree (saq_merchant_id_old);


--
-- Name: smsx_saq_survey_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_survey_id_idx ON public.saq_merchant_survey_xrefs USING btree (saq_survey_id);


--
-- Name: smsx_saq_survey_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX smsx_saq_survey_id_old_idx ON public.saq_merchant_survey_xrefs USING btree (saq_survey_id_old);


--
-- Name: sp_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sp_id_old_idx ON public.saq_prequalifications USING btree (id_old);


--
-- Name: sp_saq_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sp_saq_merchant_id_idx ON public.saq_prequalifications USING btree (saq_merchant_id);


--
-- Name: sp_saq_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sp_saq_merchant_id_old_idx ON public.saq_prequalifications USING btree (saq_merchant_id_old);


--
-- Name: sq_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sq_id_old_idx ON public.saq_questions USING btree (id_old);


--
-- Name: ss_confirmation_survey_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ss_confirmation_survey_id_idx ON public.saq_surveys USING btree (confirmation_survey_id);


--
-- Name: ss_confirmation_survey_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ss_confirmation_survey_id_old_idx ON public.saq_surveys USING btree (confirmation_survey_id_old);


--
-- Name: ss_eligibility_survey_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ss_eligibility_survey_id_idx ON public.saq_surveys USING btree (eligibility_survey_id);


--
-- Name: ss_eligibility_survey_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ss_eligibility_survey_id_old_idx ON public.saq_surveys USING btree (eligibility_survey_id_old);


--
-- Name: ss_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ss_id_old_idx ON public.saq_surveys USING btree (id_old);


--
-- Name: ssqx_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ssqx_id_old_idx ON public.saq_survey_question_xrefs USING btree (id_old);


--
-- Name: ssqx_saq_question_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ssqx_saq_question_id_idx ON public.saq_survey_question_xrefs USING btree (saq_question_id);


--
-- Name: ssqx_saq_question_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ssqx_saq_question_id_old_idx ON public.saq_survey_question_xrefs USING btree (saq_question_id_old);


--
-- Name: ssqx_saq_survey_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ssqx_saq_survey_id_idx ON public.saq_survey_question_xrefs USING btree (saq_survey_id);


--
-- Name: ssqx_saq_survey_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ssqx_saq_survey_id_old_idx ON public.saq_survey_question_xrefs USING btree (saq_survey_id_old);


--
-- Name: status; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX status ON public.tickler_leads_logs USING btree (status_id);


--
-- Name: status_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX status_idx ON public.onlineapp_applications USING btree (status);


--
-- Name: sti_shipping_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sti_shipping_type_id_idx ON public.shipping_type_items USING btree (shipping_type_id);


--
-- Name: sti_shipping_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sti_shipping_type_old_idx ON public.shipping_type_items USING btree (shipping_type_old);


--
-- Name: sys_trans_session_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX sys_trans_session_idx ON public.system_transactions USING btree (session_id);


--
-- Name: system_transaction_change_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_change_id_old_idx ON public.system_transactions USING btree (change_id_old);


--
-- Name: system_transaction_merchant_change_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_merchant_change_id_idx ON public.system_transactions USING btree (merchant_change_id);


--
-- Name: system_transaction_merchant_note_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_merchant_note_id_idx ON public.system_transactions USING btree (merchant_note_id);


--
-- Name: system_transaction_merchant_note_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_merchant_note_id_old_idx ON public.system_transactions USING btree (merchant_note_id_old);


--
-- Name: system_transaction_order_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_order_id_idx ON public.system_transactions USING btree (order_id);


--
-- Name: system_transaction_order_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_order_id_old_idx ON public.system_transactions USING btree (order_id_old);


--
-- Name: system_transaction_programming_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_programming_idx ON public.system_transactions USING btree (programming_id);


--
-- Name: system_transaction_programming_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transaction_programming_old_idx ON public.system_transactions USING btree (programming_id_old);


--
-- Name: system_transactions_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transactions_merchant_id_idx ON public.system_transactions USING btree (merchant_id);


--
-- Name: system_transactions_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transactions_merchant_id_old_idx ON public.system_transactions USING btree (merchant_id_old);


--
-- Name: system_transactions_transaction_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transactions_transaction_type_id_idx ON public.system_transactions USING btree (transaction_type_id);


--
-- Name: system_transactions_transaction_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transactions_transaction_type_old_idx ON public.system_transactions USING btree (transaction_type_old);


--
-- Name: system_transactions_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transactions_user_id_idx ON public.system_transactions USING btree (user_id);


--
-- Name: system_transactions_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX system_transactions_user_id_old_idx ON public.system_transactions USING btree (user_id_old);


--
-- Name: template_field_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX template_field_id_idx ON public.onlineapp_cobranded_application_values USING btree (template_field_id);


--
-- Name: tgates_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX tgates_merchant_id_idx ON public.tgates USING btree (merchant_id);


--
-- Name: tgates_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX tgates_merchant_id_old_idx ON public.tgates USING btree (merchant_id_old);


--
-- Name: timeline_entries_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX timeline_entries_merchant_id_idx ON public.timeline_entries USING btree (merchant_id);


--
-- Name: timeline_entries_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX timeline_entries_merchant_id_old_idx ON public.timeline_entries USING btree (merchant_id_old);


--
-- Name: timeline_entries_timeline_item_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX timeline_entries_timeline_item_id_idx ON public.timeline_entries USING btree (timeline_item_id);


--
-- Name: timeline_entries_timeline_item_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX timeline_entries_timeline_item_old_idx ON public.timeline_entries USING btree (timeline_item_old);


--
-- Name: timeline_items_timeline_item_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX timeline_items_timeline_item_old_idx ON public.timeline_items USING btree (timeline_item_old);


--
-- Name: tl_timeline_date_completed; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX tl_timeline_date_completed ON public.timeline_entries USING btree (timeline_date_completed);


--
-- Name: transaction_types_transaction_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX transaction_types_transaction_type_old_idx ON public.transaction_types USING btree (transaction_type_old);


--
-- Name: ubt_bet_code_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ubt_bet_code_old_idx ON public.user_bet_tables USING btree (bet_code_old);


--
-- Name: ubt_bet_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ubt_bet_id_idx ON public.user_bet_tables USING btree (bet_id);


--
-- Name: ubt_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ubt_user_id_idx ON public.user_bet_tables USING btree (user_id);


--
-- Name: ubt_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX ubt_user_id_old_idx ON public.user_bet_tables USING btree (user_id_old);


--
-- Name: uniq_permission; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX uniq_permission ON public.permissions USING btree (name);


--
-- Name: uniq_permission_cache; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX uniq_permission_cache ON public.permission_caches USING btree (user_id, permission_id);


--
-- Name: unique_global_client_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX unique_global_client_id_idx ON public.clients USING btree (client_id_global);


--
-- Name: unique_network_bet_tables_card_types; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX unique_network_bet_tables_card_types ON public.old_bets_backup USING btree (network_id, bet_tables_card_types_id, user_id);


--
-- Name: unique_table_card_type; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX unique_table_card_type ON public.old_bet_tables_card_types_backup USING btree (bet_table_id, card_type_id);


--
-- Name: unique_user_product_service_type; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX unique_user_product_service_type ON public.residual_product_controls USING btree (user_id, product_service_type_id);


--
-- Name: unique_uuid; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX unique_uuid ON public.onlineapp_cobranded_applications USING btree (uuid);


--
-- Name: usaepay_rep_gtwy_add_costs_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX usaepay_rep_gtwy_add_costs_id_old_idx ON public.usaepay_rep_gtwy_add_costs USING btree (id_old);


--
-- Name: usaepay_rep_gtwy_costs_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX usaepay_rep_gtwy_costs_id_old_idx ON public.usaepay_rep_gtwy_costs USING btree (id_old);


--
-- Name: user_bet_table_old_pk; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX user_bet_table_old_pk ON public.user_bet_tables USING btree (bet_code_old, user_id_old, network_old);


--
-- Name: user_bet_table_pk; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX user_bet_table_pk ON public.user_bet_tables USING btree (bet_id, user_id, network_id);


--
-- Name: user_comp_profile_partner_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_comp_profile_partner_user_id_idx ON public.user_compensation_profiles USING btree (partner_user_id);


--
-- Name: user_comp_profile_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_comp_profile_user_id_idx ON public.user_compensation_profiles USING btree (user_id);


--
-- Name: user_costs_archive_merchant_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_costs_archive_merchant_index ON public.user_costs_archives USING btree (merchant_id);


--
-- Name: user_costs_archive_merchant_pricing_archive_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_costs_archive_merchant_pricing_archive_id_index ON public.user_costs_archives USING btree (merchant_pricing_archive_id);


--
-- Name: user_costs_archive_user_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_costs_archive_user_index ON public.user_costs_archives USING btree (user_id);


--
-- Name: user_id; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_id ON public.tickler_comments USING btree (user_id);


--
-- Name: user_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_index ON public.residual_product_controls USING btree (user_id);


--
-- Name: user_parameter_types_name_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX user_parameter_types_name_key ON public.user_parameter_types USING btree (name);


--
-- Name: user_risk_assessment_merchant_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_risk_assessment_merchant_index ON public.users_products_risks USING btree (merchant_id);


--
-- Name: user_risk_assessment_user_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_risk_assessment_user_id_index ON public.users_products_risks USING btree (user_id);


--
-- Name: user_types_user_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX user_types_user_type_old_idx ON public.user_types USING btree (user_type_old);


--
-- Name: username_key; Type: INDEX; Schema: public; Owner: axia
--

CREATE UNIQUE INDEX username_key ON public.onlineapp_multipasses USING btree (username);


--
-- Name: users_email_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_email_idx ON public.users USING btree (user_email);


--
-- Name: users_lastname_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_lastname_idx ON public.users USING btree (user_last_name);


--
-- Name: users_parent_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_parent_user_id_idx ON public.users USING btree (parent_user_id);


--
-- Name: users_parent_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_parent_user_id_old_idx ON public.users USING btree (parent_user_id_old);


--
-- Name: users_referer_id_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_referer_id_index ON public.users USING btree (referer_id);


--
-- Name: users_secondary_parent_user_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_secondary_parent_user_id_idx ON public.users USING btree (secondary_parent_user_id);


--
-- Name: users_secondary_parent_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_secondary_parent_user_id_old_idx ON public.users USING btree (secondary_parent_user_id_old);


--
-- Name: users_user_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_user_id_old_idx ON public.users USING btree (user_id_old);


--
-- Name: users_user_type_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_user_type_id_idx ON public.users USING btree (user_type_id);


--
-- Name: users_user_type_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_user_type_old_idx ON public.users USING btree (user_type_old);


--
-- Name: users_username_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX users_username_idx ON public.users USING btree (username);


--
-- Name: uw_approvalinfo_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_id_old_index ON public.uw_approvalinfos USING btree (id_old);


--
-- Name: uw_approvalinfo_merchant_xrefs_approvalinfo_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_merchant_xrefs_approvalinfo_id_old_idx ON public.uw_approvalinfo_merchant_xrefs USING btree (approvalinfo_id_old);


--
-- Name: uw_approvalinfo_merchant_xrefs_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_merchant_xrefs_merchant_id_idx ON public.uw_approvalinfo_merchant_xrefs USING btree (merchant_id);


--
-- Name: uw_approvalinfo_merchant_xrefs_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_merchant_xrefs_merchant_id_old_idx ON public.uw_approvalinfo_merchant_xrefs USING btree (merchant_id_old);


--
-- Name: uw_approvalinfo_merchant_xrefs_uw_approvalinfo_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_merchant_xrefs_uw_approvalinfo_id_idx ON public.uw_approvalinfo_merchant_xrefs USING btree (uw_approvalinfo_id);


--
-- Name: uw_approvalinfo_merchant_xrefs_uw_verified_option_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_merchant_xrefs_uw_verified_option_id_idx ON public.uw_approvalinfo_merchant_xrefs USING btree (uw_verified_option_id);


--
-- Name: uw_approvalinfo_merchant_xrefs_verified_option_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_merchant_xrefs_verified_option_id_old_idx ON public.uw_approvalinfo_merchant_xrefs USING btree (verified_option_id_old);


--
-- Name: uw_approvalinfo_priority_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_priority_index ON public.uw_approvalinfos USING btree (priority);


--
-- Name: uw_approvalinfo_verified_type_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_approvalinfo_verified_type_index ON public.uw_approvalinfos USING btree (verified_type);


--
-- Name: uw_infodoc_merchant_xrefs_infodoc_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_merchant_xrefs_infodoc_id_old_idx ON public.uw_infodoc_merchant_xrefs USING btree (infodoc_id_old);


--
-- Name: uw_infodoc_merchant_xrefs_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_merchant_xrefs_merchant_id_idx ON public.uw_infodoc_merchant_xrefs USING btree (merchant_id);


--
-- Name: uw_infodoc_merchant_xrefs_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_merchant_xrefs_merchant_id_old_idx ON public.uw_infodoc_merchant_xrefs USING btree (merchant_id_old);


--
-- Name: uw_infodoc_merchant_xrefs_received_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_merchant_xrefs_received_id_old_idx ON public.uw_infodoc_merchant_xrefs USING btree (received_id_old);


--
-- Name: uw_infodoc_merchant_xrefs_uw_infodoc_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_merchant_xrefs_uw_infodoc_id_idx ON public.uw_infodoc_merchant_xrefs USING btree (uw_infodoc_id);


--
-- Name: uw_infodoc_merchant_xrefs_uw_received_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_merchant_xrefs_uw_received_id_idx ON public.uw_infodoc_merchant_xrefs USING btree (uw_received_id);


--
-- Name: uw_infodoc_priority_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_priority_index ON public.uw_infodocs USING btree (priority);


--
-- Name: uw_infodoc_required_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodoc_required_index ON public.uw_infodocs USING btree (required);


--
-- Name: uw_infodocs_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_infodocs_id_old_index ON public.uw_infodocs USING btree (id_old);


--
-- Name: uw_receiveds_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_receiveds_id_old_index ON public.uw_receiveds USING btree (id_old);


--
-- Name: uw_smx_datetime_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_smx_datetime_index ON public.uw_status_merchant_xrefs USING btree (datetime);


--
-- Name: uw_status_merchant_xrefs_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_status_merchant_xrefs_merchant_id_idx ON public.uw_status_merchant_xrefs USING btree (merchant_id);


--
-- Name: uw_status_merchant_xrefs_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_status_merchant_xrefs_merchant_id_old_idx ON public.uw_status_merchant_xrefs USING btree (merchant_id_old);


--
-- Name: uw_status_merchant_xrefs_status_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_status_merchant_xrefs_status_id_old_idx ON public.uw_status_merchant_xrefs USING btree (status_id_old);


--
-- Name: uw_status_merchant_xrefs_uw_status_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_status_merchant_xrefs_uw_status_id_idx ON public.uw_status_merchant_xrefs USING btree (uw_status_id);


--
-- Name: uw_status_priority_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_status_priority_index ON public.uw_statuses USING btree (priority);


--
-- Name: uw_statuses_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_statuses_id_old_index ON public.uw_statuses USING btree (id_old);


--
-- Name: uw_verified_option_verified_type_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_verified_option_verified_type_index ON public.uw_verified_options USING btree (verified_type);


--
-- Name: uw_verified_options_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX uw_verified_options_id_old_index ON public.uw_verified_options USING btree (id_old);


--
-- Name: vendor_rank; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX vendor_rank ON public.vendors USING btree (rank);


--
-- Name: vendors_vendor_id_old_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX vendors_vendor_id_old_index ON public.vendors USING btree (vendor_id_old);


--
-- Name: virtual_check_mid; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX virtual_check_mid ON public.virtual_checks USING btree (vc_mid);


--
-- Name: virtual_check_webs_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX virtual_check_webs_merchant_id_idx ON public.virtual_check_webs USING btree (merchant_id);


--
-- Name: virtual_check_webs_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX virtual_check_webs_merchant_id_old_idx ON public.virtual_check_webs USING btree (merchant_id_old);


--
-- Name: virtual_checks_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX virtual_checks_merchant_id_idx ON public.virtual_checks USING btree (merchant_id);


--
-- Name: virtual_checks_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX virtual_checks_merchant_id_old_idx ON public.virtual_checks USING btree (merchant_id_old);


--
-- Name: volume_tier_product_service_type_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX volume_tier_product_service_type_index ON public.residual_volume_tiers USING btree (product_service_type_id);


--
-- Name: web_ach_rep_cost_user_comp_index; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX web_ach_rep_cost_user_comp_index ON public.web_ach_rep_costs USING btree (user_compensation_profile_id);


--
-- Name: webpasses_merchant_id_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX webpasses_merchant_id_idx ON public.webpasses USING btree (merchant_id);


--
-- Name: webpasses_merchant_id_old_idx; Type: INDEX; Schema: public; Owner: axia
--

CREATE INDEX webpasses_merchant_id_old_idx ON public.webpasses USING btree (merchant_id_old);


--
-- Name: external_record_fields fk_associated_external_record_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.external_record_fields
    ADD CONSTRAINT fk_associated_external_record_id FOREIGN KEY (associated_external_record_id) REFERENCES public.associated_external_records(id) ON DELETE CASCADE;


--
-- Name: invoice_items fk_merchant_ach_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.invoice_items
    ADD CONSTRAINT fk_merchant_ach_id FOREIGN KEY (merchant_ach_id) REFERENCES public.merchant_aches(id) ON DELETE CASCADE;


--
-- Name: imported_data_collections fk_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.imported_data_collections
    ADD CONSTRAINT fk_merchant_id FOREIGN KEY (merchant_id) REFERENCES public.merchants(id) ON DELETE CASCADE;


--
-- Name: associated_external_records fk_merchant_id; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.associated_external_records
    ADD CONSTRAINT fk_merchant_id FOREIGN KEY (merchant_id) REFERENCES public.merchants(id) ON DELETE CASCADE;


--
-- Name: user_costs_archives fk_uca_merchant_pricing_archives; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.user_costs_archives
    ADD CONSTRAINT fk_uca_merchant_pricing_archives FOREIGN KEY (merchant_pricing_archive_id) REFERENCES public.merchant_pricing_archives(id) ON UPDATE RESTRICT ON DELETE CASCADE;


--
-- Name: onlineapp_users_onlineapp_cobrands onlineapp_users_onlineapp_cobrands_cobrand_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_cobrands
    ADD CONSTRAINT onlineapp_users_onlineapp_cobrands_cobrand_id_fkey FOREIGN KEY (cobrand_id) REFERENCES public.onlineapp_cobrands(id);


--
-- Name: onlineapp_users_onlineapp_cobrands onlineapp_users_onlineapp_cobrands_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_cobrands
    ADD CONSTRAINT onlineapp_users_onlineapp_cobrands_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.onlineapp_users(id);


--
-- Name: onlineapp_users_onlineapp_templates onlineapp_users_onlineapp_templates_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_templates
    ADD CONSTRAINT onlineapp_users_onlineapp_templates_template_id_fkey FOREIGN KEY (template_id) REFERENCES public.onlineapp_templates(id);


--
-- Name: onlineapp_users_onlineapp_templates onlineapp_users_onlineapp_templates_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: axia
--

ALTER TABLE ONLY public.onlineapp_users_onlineapp_templates
    ADD CONSTRAINT onlineapp_users_onlineapp_templates_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.onlineapp_users(id);


--
-- PostgreSQL database dump complete
--

