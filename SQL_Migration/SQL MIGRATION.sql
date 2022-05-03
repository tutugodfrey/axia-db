-- sudo apt-get install postgresql-contrib
-- sudo su postgres
-- createuser -p 5433 -P -E -S -D -R -e axia
-- psql -p 5433
-- CREATE DATABASE axia OWNER axia TEMPLATE template0 LC_COLLATE = 'C' LC_CTYPE = 'C';
-- GRANT ALL ON DATABASE axia TO axia;
-- Install pglink
-- \i /usr/share/postgresql/9.1/extension/dblink--1.0.sql
--\i /usr/share/postgresql/9.1/extension/uuid-ossp--1.0.sql
-- \c axia
-- CREATE EXTENSION dblink;
-- GRANT EXECUTE ON FUNCTION dblink_connect_u(text,text) TO axia;
-- CREATE EXTENSION "uuid-ossp";
-- 
-- CREATE Link to postgresql 8.4 database
--
\timing
select dblink_connect_u('pg84', 'dbname=axia_legacy');

--
-- Migrate Old tables to new tables
--

--
-- DROP VIEWS
--

--  DROP VIEW axia_compliants CASCADE;
--  DROP VIEW saq_eligibles CASCADE;
 --DROP VIEW compliant_merchants CASCADE;
 --DROP VIEW compliant_merchants_cs CASCADE;
 --DROP VIEW non_compliant_merchants CASCADE;
 --DROP VIEW non_compliant_merchants_cs CASCADE;
--  DROP VIEW pci_reminders CASCADE;
--  DROP VIEW reminders CASCADE;
--  DROP VIEW saq_eligible_wrongs CASCADE;
--
-- Make sure that all Foreign Key Constraints are gone
--

--  ALTER TABLE bankcards DROP CONSTRAINT bankcard_bc_gw_gateway_id_fkey;
--  ALTER TABLE bankcards DROP CONSTRAINT bankcard_bc_usaepay_rep_gtwy_add_cost_id_fkey;
--  ALTER TABLE bankcards DROP CONSTRAINT bankcard_bc_usaepay_rep_gtwy_cost_id_fkey;
--  ALTER TABLE discovers DROP CONSTRAINT discover_disc_gw_gateway_id_fkey;
--  ALTER TABLE equipment_programmings DROP CONSTRAINT equipment_programming_gateway_id_fkey;
--  ALTER TABLE gateway0s DROP CONSTRAINT gateway_gw_usaepay_rep_gtwy_add_cost_id_fkey;
--  ALTER TABLE gateway0s DROP CONSTRAINT gateway_gw_usaepay_rep_gtwy_cost_id_fkey;
--  ALTER TABLE gateway1s DROP CONSTRAINT gateway1s_gw1_gateway_id_fkey;
--  ALTER TABLE gateway2s DROP CONSTRAINT gateway2_gw2_gateway_id_fkey;
--  ALTER TABLE merchant_cancellations DROP CONSTRAINT merchant_cancellation_subreason_id_fkey;
--  ALTER TABLE merchant_reject_lines DROP CONSTRAINT merchant_reject_line_reject_id_fkey;
--  ALTER TABLE merchant_reject_lines DROP CONSTRAINT merchant_reject_line_statusid_fkey;
--  ALTER TABLE merchant_rejects DROP CONSTRAINT merchant_reject_recurrance_id_fkey;
--  ALTER TABLE merchant_rejects DROP CONSTRAINT merchant_reject_type_id_fkey;
--  ALTER TABLE merchant_uws DROP CONSTRAINT merchant_uw_final_approved_id_fkey;
--  ALTER TABLE merchant_uws DROP CONSTRAINT merchant_uw_final_status_id_fkey;
--  ALTER TABLE merchants DROP CONSTRAINT merchant_app_id_fkey;
--  ALTER TABLE onlineapp_apips DROP CONSTRAINT onlineapp_apips_user_id_fkey;
--  ALTER TABLE onlineapp_email_timelines DROP CONSTRAINT onlineapp_email_timelines_app_id_fkey;
--  ALTER TABLE onlineapp_email_timelines DROP CONSTRAINT onlineapp_email_timelines_subject_id_fkey;
--  ALTER TABLE onlineapp_epayments DROP CONSTRAINT onlineapp_epayments_user_id_fkey;
--  ALTER TABLE pci_billings DROP CONSTRAINT pci_billing_pci_billing_type_id_fkey;
--  ALTER TABLE pci_compliances DROP CONSTRAINT pci_compliance_pci_compliance_date_type_id_fkey;
--  ALTER TABLE pci_compliances DROP CONSTRAINT pci_compliance_saq_merchant_id_fkey;
--  ALTER TABLE saq_answers DROP CONSTRAINT fkey_saq_answer_survey_question_xref_id;
--  ALTER TABLE saq_answers DROP CONSTRAINT fkey_saq_merchant_survey_xref_id;
--  ALTER TABLE saq_merchant_pci_email_sent DROP CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_id_fkey;
--  ALTER TABLE saq_merchant_pci_email_sent DROP CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_pci_email_id_fkey;
--  ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_confirmation_survey_id;
--  ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_eligibility_survey_id;
--  ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_merchant_id;
--  ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_survey_id;
--  ALTER TABLE saq_prequalifications DROP CONSTRAINT fkey_saq_prequalification_merchant_id;
--  ALTER TABLE saq_survey_question_xrefs DROP CONSTRAINT fkey_saq_survey_question_xref_saq_question_id;
--  ALTER TABLE saq_survey_question_xrefs DROP CONSTRAINT fkey_saq_survey_question_xref_saq_survey_id;
--  ALTER TABLE saq_surveys DROP CONSTRAINT fkey_saq_survey_confirmation_survey_id;
--  ALTER TABLE saq_surveys DROP CONSTRAINT fkey_saq_survey_eligibility_survey_id;
--  ALTER TABLE uw_approvalinfo_merchant_xrefs DROP CONSTRAINT uw_approvalinfo_merchant_xref_approvalinfo_id_fkey;
--  ALTER TABLE uw_approvalinfo_merchant_xrefs DROP CONSTRAINT uw_approvalinfo_merchant_xref_verified_option_id_fkey;
--  ALTER TABLE uw_infodoc_merchant_xrefs DROP CONSTRAINT uw_infodoc_merchant_xref_infodoc_id_fkey;
--  ALTER TABLE uw_infodoc_merchant_xrefs DROP CONSTRAINT uw_infodoc_merchant_xref_received_id_fkey;
--  ALTER TABLE uw_status_merchant_xrefs DROP CONSTRAINT uw_status_merchant_xref_status_id_fkey;


--
-- Migrate achs
--
INSERT INTO public.achs (merchant_id,ach_mid,ach_expected_annual_sales,ach_average_transaction,ach_estimated_max_transaction,ach_written_pre_auth,ach_nonwritten_pre_auth,ach_merchant_initiated_perc,ach_consumer_initiated_perc,ach_monthly_gateway_fee,ach_monthly_minimum_fee,ach_statement_fee,ach_batch_upload_fee,ach_reject_fee,ach_add_bank_orig_ident_fee,ach_file_fee,ach_eft_ccd_nw,ach_eft_ccd_w,ach_eft_ppd_nw,ach_eft_ppd_w,ach_application_fee,ach_expedite_fee,ach_tele_training_fee,ach_mi_w_dsb_bank_name,ach_mi_w_dsb_routing_number,ach_mi_w_dsb_account_number,ach_mi_w_fee_bank_name,ach_mi_w_fee_routing_number,ach_mi_w_fee_account_number,ach_mi_w_rej_bank_name,ach_mi_w_rej_routing_number,ach_mi_w_rej_account_number,ach_mi_nw_dsb_account_number,ach_mi_nw_dsb_routing_number,ach_mi_nw_dsb_bank_name,ach_mi_nw_fee_bank_name,ach_mi_nw_fee_routing_number,ach_mi_nw_fee_account_number,ach_mi_nw_rej_bank_name,ach_mi_nw_rej_routing_number,ach_mi_nw_rej_account_number,ach_ci_w_dsb_bank_name,ach_ci_w_dsb_routing_number,ach_ci_w_dsb_account_number,ach_ci_w_fee_bank_name,ach_ci_w_fee_routing_number,ach_ci_w_fee_account_number,ach_ci_w_rej_bank_name,ach_ci_w_rej_routing_number,ach_ci_w_rej_account_number,ach_ci_nw_dsb_bank_name,ach_ci_nw_dsb_routing_number,ach_ci_nw_dsb_account_number,ach_ci_nw_fee_bank_name,ach_ci_nw_fee_routing_number,ach_ci_nw_fee_account_number,ach_ci_nw_rej_bank_name,ach_ci_nw_rej_routing_number,ach_ci_nw_rej_account_number,ach_rate)
SELECT merchant_id,ach_mid,ach_expected_annual_sales,ach_average_transaction,ach_estimated_max_transaction,ach_written_pre_auth,ach_nonwritten_pre_auth,ach_merchant_initiated_perc,ach_consumer_initiated_perc,ach_monthly_gateway_fee,ach_monthly_minimum_fee,ach_statement_fee,ach_batch_upload_fee,ach_reject_fee,ach_add_bank_orig_id_fee,ach_file_fee,ach_eft_ccd_nw,ach_eft_ccd_w,ach_eft_ppd_nw,ach_eft_ppd_w,ach_application_fee,ach_expedite_fee,ach_tele_training_fee,ach_mi_w_dsb_bank_name,ach_mi_w_dsb_routing_number,ach_mi_w_dsb_account_number,ach_mi_w_fee_bank_name,ach_mi_w_fee_routing_number,ach_mi_w_fee_account_number,ach_mi_w_rej_bank_name,ach_mi_w_rej_routing_number,ach_mi_w_rej_account_number,ach_mi_nw_dsb_account_number,ach_mi_nw_dsb_routing_number,ach_mi_nw_dsb_bank_name,ach_mi_nw_fee_bank_name,ach_mi_nw_fee_routing_number,ach_mi_nw_fee_account_number,ach_mi_nw_rej_bank_name,ach_mi_nw_rej_routing_number,ach_mi_nw_rej_account_number,ach_ci_w_dsb_bank_name,ach_ci_w_dsb_routing_number,ach_ci_w_dsb_account_number,ach_ci_w_fee_bank_name,ach_ci_w_fee_routing_number,ach_ci_w_fee_account_number,ach_ci_w_rej_bank_name,ach_ci_w_rej_routing_number,ach_ci_w_rej_account_number,ach_ci_nw_dsb_bank_name,ach_ci_nw_dsb_routing_number,ach_ci_nw_dsb_account_number,ach_ci_nw_fee_bank_name,ach_ci_nw_fee_routing_number,ach_ci_nw_fee_account_number,ach_ci_nw_rej_bank_name,ach_ci_nw_rej_routing_number,ach_ci_nw_rej_account_number,ach_rate 
FROM axia_legacy. dblink('pg84', 'select merchant_id,ach_mid,ach_expected_annual_sales,ach_average_transaction,ach_estimated_max_transaction,ach_written_pre_auth,ach_nonwritten_pre_auth,ach_merchant_initiated_perc,ach_consumer_initiated_perc,ach_monthly_gateway_fee,ach_monthly_minimum_fee,ach_statement_fee,ach_batch_upload_fee,ach_reject_fee,ach_add_bank_orig_id_fee,ach_file_fee,ach_eft_ccd_nw,ach_eft_ccd_w,ach_eft_ppd_nw,ach_eft_ppd_w,ach_application_fee,ach_expedite_fee,ach_tele_training_fee,ach_mi_w_dsb_bank_name,ach_mi_w_dsb_routing_number,ach_mi_w_dsb_account_number,ach_mi_w_fee_bank_name,ach_mi_w_fee_routing_number,ach_mi_w_fee_account_number,ach_mi_w_rej_bank_name,ach_mi_w_rej_routing_number,ach_mi_w_rej_account_number,ach_mi_nw_dsb_account_number,ach_mi_nw_dsb_routing_number,ach_mi_nw_dsb_bank_name,ach_mi_nw_fee_bank_name,ach_mi_nw_fee_routing_number,ach_mi_nw_fee_account_number,ach_mi_nw_rej_bank_name,ach_mi_nw_rej_routing_number,ach_mi_nw_rej_account_number,ach_ci_w_dsb_bank_name,ach_ci_w_dsb_routing_number,ach_ci_w_dsb_account_number,ach_ci_w_fee_bank_name,ach_ci_w_fee_routing_number,ach_ci_w_fee_account_number,ach_ci_w_rej_bank_name,ach_ci_w_rej_routing_number,ach_ci_w_rej_account_number,ach_ci_nw_dsb_bank_name,ach_ci_nw_dsb_routing_number,ach_ci_nw_dsb_account_number,ach_ci_nw_fee_bank_name,ach_ci_nw_fee_routing_number,ach_ci_nw_fee_account_number,ach_ci_nw_rej_bank_name,ach_ci_nw_rej_routing_number,ach_ci_nw_rej_account_number,ach_rate FROM axia_legacy. ach')
as t1(
    merchant_id varchar(50),
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

DELETE FROM public.achs where merchant_id like '%-%';
DELETE FROM public.achs where merchant_id = 'BECKER';

--
-- Convert columns FROM axia_legacy. varchar to integer
--
--ALTER TABLE achs ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
--ALTER TABLE achs ALTER COLUMN ach_mid TYPE bigint USING CAST(ach_mid AS bigint);

--
-- Migrate address_type
--
-- INSERT INTO public.address_types (address_type, address_type_description)
-- SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. address_type')
-- as t1 (
--  address_type character varying(36),
--  address_type_description character varying(50)
-- );

INSERT INTO public.address_types VALUES ('795f442e-04ab-43d1-a7f8-f9ba685b90ac', 'BUS', 'Business Address');
INSERT INTO public.address_types VALUES ('1c7c0709-86df-4643-8f7f-78bd28259008', 'CORP', 'Corporate Address');
INSERT INTO public.address_types VALUES ('31e277ff-423a-4af8-9042-8310d7c320df', 'OWN', 'Owner Address');
INSERT INTO public.address_types VALUES ('63494506-9aed-4d7c-b83c-898e6254ff20', 'BANK', 'Bank Address');
INSERT INTO public.address_types VALUES ('edf125da-8592-42e3-bd26-ff0614bd27ba', 'MAIL', 'Mail Address');

--
-- Migrate address
--

-- ALTER TABLE addresses RENAME COLUMN owner_id to merchant_owner_id;

INSERT INTO public.addresses (address_id,merchant_id,address_type_id,merchant_owner_id,address_title,address_street,address_city,address_state,address_zip,address_phone,address_fax,address_phone2,address_phone_ext,address_phone2_ext)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. address')
as t1 (
    address_id integer,
    merchant_id character varying(50),
    address_type character varying(36),
    merchant_owner_id integer,
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

DELETE FROM public.addresses WHERE merchant_id like '''%';
DELETE FROM public.addresses where merchant_id LIKE 'TEST%';
DELETE FROM public.addresses where merchant_id LIKE '%+%';
DELETE FROM public.addresses where merchant_id LIKE '%-%';
DELETE FROM public.addresses where merchant_id = '';
DELETE FROM public.addresses where char_length(merchant_id) > 16;

--ALTER TABLE addresses ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate Adjustments
--
INSERT INTO public.adjustments (adj_seq_number,user_id,adj_date,adj_description,adj_amount)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. adjustments ORDER BY adj_seq_number ASC')
as t1 (
    adj_seq_number integer,
    user_id integer,
    adj_date date,
    adj_description character varying(100),
    adj_amount real
);

--
-- Migrate admin_entity_view
--

INSERT INTO public.admin_entity_views (user_id, entity_id)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. admin_entity_view')
as t1 (
    user_id integer,
    entity varchar(36)
);

--
-- Migrate amex
--

INSERT INTO public.amexes(merchant_id,amex_processing_rate,amex_per_item_fee)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. amex')
as t1 (
    merchant_id character varying(50),
    amex_processing_rate real,
    amex_per_item_fee real
);

--
-- Convert amexes.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE amexes ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate authorize
--

INSERT INTO public.authorizes(merchant_id,mid,transaction_fee,monthly_fee)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. authorize')
as t1(
    merchant_id character varying(50),
    mid character varying(20),
    transaction_fee real,
    monthly_fee real
);

--
-- Convert authorizes.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE authorizes ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate bankcard
--
--DELETE FROM public.bankcards where bc_pt_mobile_transaction_fee > 100000; --Remove corrupted integers. 

INSERT INTO public.bankcards (merchant_id,bc_mid,bet_code,bc_processing_rate,bc_per_item_fee,bc_monthly_volume,bc_average_ticket,bc_max_transaction_amount,bc_card_present_swipe,bc_card_not_present,bc_card_present_imprint,bc_direct_to_consumer,bc_business_to_business,bc_government,bc_annual_fee,bc_statement_fee,bc_min_month_process_fee,bc_chargeback_fee,bc_aru_fee,bc_voice_auth_fee,bc_te_amex_number,bc_te_amex_auth_fee,bc_te_diners_club_number,bc_te_diners_club_auth_fee,bc_te_discover_number,bc_te_discover_auth_fee,bc_te_jcb_number,bc_te_jcb_auth_fee,bc_pt_monthly_support_fee,bc_pt_online_mer_report_fee,bc_pt_mobile_access_fee,bc_pt_mobile_transaction_fee,bc_pt_application_fee,bc_pt_expedite_fee,bc_pt_mobile_setup_fee,bc_pt_equip_fee,bc_pt_phys_prod_tele_train_fee,bc_pt_equip_reprog_fee,bc_vt_monthly_support_fee,bc_vt_gateway_access_fee,bc_vt_application_fee,bc_vt_expedite_fee,bc_vt_prod_tele_train_fee,bc_vt_lease_rental_deposit,bc_hidden_per_item_fee,bc_te_amex_number_disp,bc_te_diners_club_number_disp,bc_te_discover_number_disp,bc_te_jcb_number_disp,bc_hidden_per_item_fee_cross,bc_eft_secure_flag,bc_pos_partner_flag,bc_pt_online_rep_report_fee,bc_micros_ip_flag,bc_micros_dialup_flag,bc_micros_per_item_fee,bc_te_num_items,bc_wireless_flag,bc_wireless_terminals,bc_usaepay_flag,bc_epay_retail_flag,bc_usaepay_rep_gtwy_cost_id,bc_usaepay_rep_gtwy_add_cost_id,bc_card_not_present_internet,bc_tgate_flag,bc_petro_flag,bc_risk_assessment,gateway_id,bc_gw_rep_rate,bc_gw_rep_per_item,bc_gw_rep_statement,bc_gw_rep_features)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. bankcard')
as t1(
    merchant_id character varying(50),
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


DELETE FROM public.bankcards WHERE merchant_id = 'BECKER';
DELETE FROM public.bankcards WHERE merchant_id like '''%';
DELETE FROM public.bankcards where merchant_id LIKE 'TEST%';
DELETE FROM public.bankcards where merchant_id LIKE '%+%';
DELETE FROM public.bankcards where merchant_id LIKE '%-%';
DELETE FROM public.bankcards where merchant_id = '';
DELETE FROM public.bankcards where char_length(merchant_id) > 16;
DELETE FROM public.bankcards where bc_mid is NULL;

--
-- Convert bankcards.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE bankcards ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate bet
--

INSERT INTO public.bets(bet_code,bet_processing_rate,bet_processing_rate_2,bet_processing_rate_3,bet_per_item_fee,bet_per_item_fee_2,bet_per_item_fee_3,bet_business_rate,bet_business_rate_2,bet_per_item_fee_bus,bet_per_item_fee_bus_2,bet_extra_pct)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. bet')
as t1(
    bet_code character varying(50),
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


--
-- Migrate cancellation_fee
--

INSERT INTO public.cancellation_fees(cancellation_fee_id,cancellation_fee_description)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. cancellation_fee ORDER BY cancellation_fee_id ASC')
as t1(
    cancellation_fee_id integer,
    cancellation_fee_description character varying(50)
);

--
-- Migrate card_type
--

INSERT INTO public.card_types(card_type,card_type_description)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. card_type')
as t1(
    card_type character varying(36),
    card_type_description character varying(20)
);

UPDATE card_types set id = 'e9580d94-29ce-4de4-9fd4-c81d1afefbf4' where card_type_description = 'Visa';
UPDATE card_types set id = 'd82f4282-f816-4880-975c-53d42a7f02bc' where card_type_description = 'Discover';


--
-- Migrate change_type
--

INSERT INTO public.change_types(change_type_old_id,change_type_description)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. change_type ORDER BY change_type ASC')
as t1(
    change_type integer,
    change_type_description character varying(50)
);

--
-- Migrate check_guarantee
-- 

INSERT INTO public.check_guarantees(merchant_id,cg_mid,cg_station_number,cg_account_number,cg_transaction_rate,cg_per_item_fee,cg_monthly_fee,cg_monthly_minimum_fee,cg_application_fee)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. check_guarantee')
as t1(
    merchant_id character varying(50),
    cg_mid character varying(20),
    cg_station_number character varying(20),
    cg_account_number character varying(20),
    cg_transaction_rate real,
    cg_per_item_fee real,
    cg_monthly_fee real,
    cg_monthly_minimum_fee real,
    cg_application_fee real
);

DELETE FROM public.check_guarantees WHERE merchant_id = 'BECKER';
--
-- Convert check_guarantees.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE check_guarantees ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate commission_pricing
--

INSERT INTO public.commission_pricings (merchant_id,user_id,c_month,c_year,multiple,r_rate_pct,r_per_item_fee,r_statement_fee,m_rate_pct,m_per_item_fee,m_statement_fee,m_avg_ticket,m_monthly_volume,m_bet_code,ref_seq_number,bet_extra_pct,res_seq_number,products_services_type_id,num_items,ref_p_value,res_p_value,r_risk_assessment,ref_p_type,res_p_type,ref_p_pct,res_p_pct)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. commission_pricing')
as t1(
    merchant_id character varying(50),
    user_id integer,
    c_month real,
    c_year real,
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
    products_services_type character varying(36),
    num_items real,
    ref_p_value real,
    res_p_value real,
    r_risk_assessment real,
    ref_p_type pp_types,
    res_p_type pp_types,
    ref_p_pct integer,
    res_p_pct integer
);

DELETE FROM public.commission_pricings where merchant_id LIKE '%+%';

--
-- Convert commission_pricings.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE commission_pricings ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate commission_report
--
INSERT INTO public.commission_reports(c_month,c_year,c_retail,c_rep_cost,c_shipping,c_app_rtl,c_app_cost,c_install,c_expecting,c_from_nw1,c_other_cost,c_from_other,c_business,user_id,status,order_id,axia_invoice_number,ach_seq_number,description,merchant_id,split_commissions,ref_seq_number,res_seq_number,partner_id,partner_exclude_volume)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. commission_report')
as t1 (
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
    split_commissions boolean,
    ref_seq_number integer,
    res_seq_number integer,
    partner_id integer,
    partner_exclude_volume boolean
);

DELETE FROM public.commission_reports WHERE merchant_id = 'BECKER';
--DELETE FROM public.commission_reports WHERE merchant_id like '''%';
DELETE FROM public.commission_reports where merchant_id LIKE 'TEST%';
DELETE FROM public.commission_reports where merchant_id LIKE '%+%';
DELETE FROM public.commission_reports where merchant_id LIKE '%-%';
DELETE FROM public.commission_reports where merchant_id = '';
DELETE FROM public.commission_reports where char_length(merchant_id) > 16;
DELETE FROM public.commission_reports WHERE ach_seq_number in (30026,28536,23219,22404,21847,21615,20072,19464,19265,17736,17636,17635,17634,17528,17520,12767,17873,3017);
--
-- Convert commission_pricings.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE commission_reports ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate debit_acquirer
--
INSERT INTO public.debit_acquirers(debit_acquirer_id,debit_acquirers)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. debit_acquirer')
as t1(
    debit_acquirer_id integer,
    debit_acquirers character varying(137)
);

--
-- Migrate debit
--

INSERT INTO public.debits (merchant_id,mid,transaction_fee,monthly_fee,monthly_volume,monthly_num_items,rate_pct,acquirer_id)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. debit')
as t1 (
    merchant_id character varying(50),
    mid character varying(20),
    transaction_fee real,
    monthly_fee real,
    monthly_volume real,
    monthly_num_items real,
    rate_pct real,
    acquirer_id integer
);
--
-- Convert debit.merchant_id FROM axia_legacy. varchar to integer
--

DELETE FROM public.debits WHERE merchant_id = 'BECKER';
DELETE FROM public.debits WHERE merchant_id like '''%';
DELETE FROM public.debits where merchant_id LIKE 'TEST%';
DELETE FROM public.debits where merchant_id LIKE '%+%';
DELETE FROM public.debits where merchant_id LIKE '%-%';
DELETE FROM public.debits where merchant_id = '';
DELETE FROM public.debits where char_length(merchant_id) > 16;

--ALTER TABLE debits ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate discover table
--

INSERT INTO public.discovers(merchant_id,bet_code,disc_processing_rate,disc_per_item_fee,disc_statement_fee,disc_monthly_volume,disc_average_ticket,disc_risk_assessment,gateway_id,disc_gw_rep_rate,disc_gw_rep_per_item,disc_gw_rep_features)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. discover')
as t1 (
    merchant_id character varying(50),
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

DELETE FROM public.discovers WHERE merchant_id = 'BECKER';
DELETE FROM public.discovers WHERE merchant_id like '''%';
DELETE FROM public.discovers where merchant_id LIKE 'TEST%';
DELETE FROM public.discovers where merchant_id LIKE '%+%';
DELETE FROM public.discovers where merchant_id LIKE '%-%';
DELETE FROM public.discovers where merchant_id = '';
DELETE FROM public.discovers where char_length(merchant_id) > 16;


--ALTER TABLE discovers ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
--
-- Migrate discover_bet
--

INSERT INTO public.discover_bets(bet_code,bet_extra_pct)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. discover_bet')
as t1 (
    bet_code character varying(50),
    bet_extra_pct real
);

--
-- Migrate discover_user_bet_table
--

INSERT INTO public.discover_user_bet_tables (bet_code,user_id,network,rate,pi)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. discover_user_bet_table')
as t1 (
    bet_code character varying(50),
    user_id integer,
    network character varying(20),
    rate real,
    pi real
);


--
-- Migrate ebt
--
INSERT INTO public.ebts (merchant_id,mid,transaction_fee,monthly_fee)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. ebt')
as t1 (
    merchant_id character varying(50),
    mid character varying(20),
    transaction_fee real,
    monthly_fee real
);

--
-- Convert ebts.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE ebts ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate entity
--

INSERT INTO public.entities (entity,entity_name)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. entity')
as t1(
    entity varchar(36),
    entity_name varchar(50)
);


--
-- Migrate equipment_item
--

INSERT INTO public.equipment_items (equipment_item_old_id,equipment_type,equipment_item_description,equipment_item_true_price,equipment_item_rep_price,active,warranty)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. equipment_item ORDER BY equipment_item ASC')
as t1(
    equipment_item integer,
    equipment_type character varying(36),
    equipment_item_description character varying(100),
    equipment_item_true_price real,
    equipment_item_rep_price real,
    active integer,
    warranty integer
);

--
-- Migrate equipment_programming_type_xref
--
--ALTER TABLE equipment_programming_type_xrefs RENAME COLUMN programming_id to equipment_programming_id;

INSERT INTO public.equipment_programming_type_xrefs(equipment_programming_id,programming_type)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. equipment_programming_type_xref ORDER BY programming_id ASC')
as t1(
    equipment_programming_id integer,
    programming_type varchar(36)
);

--
-- migrate equipment_programming
--
-- ALTER TABLE equipment_programmings RENAME COLUMN app_id to app_type;

INSERT INTO public.equipment_programmings (programming_id,merchant_id,terminal_number,hardware_serial,terminal_type,network,provider,app_type,status,date_entered,date_changed,user_id,serial_number,pin_pad,printer,auto_close,chain,agent,gateway_id,version)
--SELECT programming_id as id,programming_id,merchant_id,terminal_number,hardware_serial,terminal_type,network,provider,app_id,status,date_entered,date_changed,user_id,serial_number,pin_pad,printer,auto_close,chain,agent,gateway_id,version 
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. equipment_programming ORDER BY programming_id ASC')
as t1(
    programming_id integer,
    merchant_id character varying(50),
    terminal_number character varying(20),
    hardware_serial character varying(20),
    terminal_type character varying(20),
    network character varying(20),
    provider character varying(20),
    app_type character varying(20),
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

--
-- Drop non-integer values FROM axia_legacy. merchant_id
--
DELETE FROM public.equipment_programmings WHERE merchant_id = 'BECKER';
DELETE FROM public.equipment_programmings WHERE merchant_id like '''%';
DELETE FROM public.equipment_programmings where merchant_id LIKE 'TEST%';
DELETE FROM public.equipment_programmings where merchant_id LIKE '%+%';
DELETE FROM public.equipment_programmings where merchant_id LIKE '%-%';
DELETE FROM public.equipment_programmings where merchant_id = '';
DELETE FROM public.equipment_programmings where char_length(merchant_id) > 16;

--
-- Convert equipment_programmings.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE equipment_programmings ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
--
-- Set Appropriate values for equipment_programmings_id_seq
--

-- SELECT setval('equipment_programmings_id_seq', (SELECT id FROM axia_legacy. equipment_programmings ORDER BY id DESC LIMIT 1));

--
-- Migrate equipment_type
--

INSERT INTO public.equipment_types(equipment_type_old_id,equipment_type_description)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. equipment_type')
as t1 (
    equipment_type character varying(36),
    equipment_type_description character varying(50)
);

--
-- Migrate gateways
--

INSERT INTO public.gateways(old_id,name)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. gateways')
as t1 (  
    id integer,
    name character varying(57)
);
-- SELECT setval('gateways_id_seq', (SELECT id FROM axia_legacy. gateways ORDER BY id DESC LIMIT 1));

--
-- Migrate gateway0s
--
INSERT INTO public.gateway0s (merchant_id,gw_rate,gw_per_item,gw_statement,gw_epay_retail_num,gw_usaepay_rep_gtwy_cost_id,gw_usaepay_rep_gtwy_add_cost_id)
SELECT * FROM axia_legacy. dblink('pg84','select * FROM axia_legacy. gateway')
as t1(
    merchant_id character varying(50),
    gw_rate real,
    gw_per_item real,
    gw_statement real,
    gw_epay_retail_num integer,
    gw_usaepay_rep_gtwy_cost_id integer,
    gw_usaepay_rep_gtwy_add_cost_id integer
);

--
-- Convert gateway0s.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE gateway0s ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate gateway1s
--
INSERT INTO public.gateway1s(merchant_id,gw1_rate,gw1_per_item,gw1_statement,gw1_rep_rate,gw1_rep_per_item,gw1_rep_statement,gw1_rep_features,gateway_id,gw1_monthly_volume,gw1_monthly_num_items)
SELECT * FROM axia_legacy. dblink('pg84','select * FROM axia_legacy. gateway1')
as t1(
    merchant_id character varying(50),
    gw1_rate real,
    gw1_per_item real,
    gw1_statement real,
    gw1_rep_rate real,
    gw1_rep_per_item real,
    gw1_rep_statement real,
    gw1_rep_features text,
    gw1_gateway_id integer,
    gw1_monthly_volume real,
    gw1_monthly_num_items real
);

--
-- Convert gateway1s.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE gateway1s ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate gateway2s
--
INSERT INTO public.gateway2s (merchant_id,gw2_rate,gw2_per_item,gw2_statement,gw2_rep_rate,gw2_rep_per_item,gw2_rep_statement,gw2_rep_features,gateway_id,gw2_monthly_volume,gw2_monthly_num_items)
SELECT * FROM axia_legacy. dblink('pg84','select * FROM axia_legacy. gateway2')
as t1(
    merchant_id character varying(50),
    gw2_rate real,
    gw2_per_item real,
    gw2_statement real,
    gw2_rep_rate real,
    gw2_rep_per_item real,
    gw2_rep_statement real,
    gw2_rep_features text,
    gw2_gateway_id integer,
    gw2_monthly_volume real,
    gw2_monthly_num_items real
);

--
-- Convert gateway2s.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE gateway2s ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate gift_card
--
INSERT INTO public.gift_cards (merchant_id,gc_mid,gc_magstripe_loyalty_item_fee,gc_magstripe_gift_item_fee,gc_chip_card_one_rate,gc_chip_card_gift_item_fee,gc_chip_card_loyalty_item_fee,gc_smart_card_printing,gc_mag_card_printing,gc_loyalty_mgmt_database,gc_statement_fee,gc_application_fee,gc_equipment_fee,gc_misc_supplies,gc_merch_prov_art_setup_fee,gc_news_provider_artwork_fee,gc_training_fee,gc_plan)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. gift_card')
as t1 (
    merchant_id character varying(50),
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
    gc_plan character varying(8)
);

--
-- Convert gateway2s.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE gift_cards ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate group
--

INSERT INTO public.groups (group_old_id,group_description,active)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. "group" order by CAST(group_id as INTEGER) ASC')
as t1 (
    group_id character varying(10),
    group_description character varying(50),
    active boolean
);

--
-- Increment nextval to appropriate value
--

-- SELECT setval('groups_id_seq',(select id FROM axia_legacy. "groups" order by id desc limit 1));

--
-- migrate last_deposit_report
--

INSERT INTO public.last_deposit_reports (merchant_id,merchant_dba,last_deposit_date,user_id,monthly_volume)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. last_deposit_report')
as t1 (
    merchant_id character varying(50),
    merchant_dba character varying(100),
    last_deposit_date date,
    user_id integer,
    monthly_volume numeric(10,2)
);

--
-- Convert last_deposit_reports.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE last_deposit_reports ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate merchant
--
-- ALTER TABLE merchants RENAME COLUMN bin_id to merchant_bin_id;
--ALTER TABLE merchants RENAME COLUMN merchant_fed_tax_id to merchant_tin;
--ALTER TABLE merchants RENAME COLUMN merchant_fed_tax_id_disp to merchant_tin_disp;
-- ALTER TABLE merchants RENAME COLUMN acquirer_id to merchant_acquirer_id;

INSERT INTO public.merchants (merchant_id,user_id,merchant_mid,merchant_name,merchant_dba,merchant_contact,merchant_email,merchant_ownership_type,merchant_tin,merchant_d_and_b,inactive_date,active_date,ref_seq_number,network_id,merchant_buslevel,merchant_sic,entity_id,group_id,merchant_bustype,merchant_url,merchant_tin_disp,merchant_d_and_b_disp,active,cancellation_fee_id,merchant_contact_position,merchant_mail_contact,res_seq_number,merchant_bin_id,merchant_acquirer_id,reporting_user,merchant_ps_sold,ref_p_type,ref_p_value,res_p_type,res_p_value,ref_p_pct,res_p_pct,onlineapp_application_id,partner_id,partner_exclude_volume,aggregated,cobranded_application_id)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant')
as t1 (
    merchant_id character varying(50),
    user_id integer,
    merchant_mid character varying(20),
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
    entity character varying(36),
    group_id character varying(36),
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


DELETE FROM public.merchants WHERE merchant_id like '''%';
DELETE FROM public.merchants where merchant_id LIKE 'TEST%';
DELETE FROM public.merchants where char_length(merchant_id) > 16;
--
-- Convert merchants.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE merchants ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
--ALTER TABLE merchants ALTER COLUMN group_id TYPE integer USING CAST(group_id AS integer);

--
-- Migrate merchant_ach
--

INSERT INTO public.merchant_achs(ach_seq_number,merchant_id,date_submitted,date_completed,reason,credit_amount,debit_amount,reason_other,status,user_id,invoice_number,app_status_id,billing_option_id,commission_month,tax)
SELECT ach_seq_number,merchant_id,date_submitted,date_completed,reason,credit_amount,debit_amount,reason_other,status,user_id,invoice_number,app_status_id,billing_option_id,commission_month,tax 
FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_ach')
as t1 (
    ach_seq_number integer,
    merchant_id character varying(50),
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

-- SELECT setval('merchant_achs_id_seq',(SELECT id FROM axia_legacy. merchant_achs ORDER BY id DESC LIMIT 1));
DELETE FROM public.merchant_achs WHERE merchant_id = 'BECKER';
DELETE FROM public.merchant_achs WHERE merchant_id like '''%';
DELETE FROM public.merchant_achs where merchant_id LIKE 'TEST%';
DELETE FROM public.merchant_achs where merchant_id LIKE '%+%';
DELETE FROM public.merchant_achs where merchant_id LIKE '%-%';
DELETE FROM public.merchant_achs where merchant_id = '';
DELETE FROM public.merchant_achs where char_length(merchant_id) > 16;
--
-- Convert merchant_achs.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE merchant_achs ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate merchant_ach_app_status
--
INSERT INTO public.merchant_ach_app_statuses (app_status_id,app_status_description,rank,app_true_price,app_rep_price)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_ach_app_status ORDER BY app_status_id ASC')
as t1(
    app_status_id integer,
    app_status_description character varying(50),
    rank integer,
    app_true_price real,
    app_rep_price real
);


--
-- Migrate merchant_ach_billing_option
--

INSERT INTO public.merchant_ach_billing_options (billing_option_id,billing_option_description)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_ach_billing_option order by billing_option_id ASC')
as t1(
    billing_option_id integer,
    billing_option_description character varying(50)
);

INSERT INTO public.merchant_acquirers (acquirer_id,acquirer)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_acquirer ORDER BY acquirer_id ASC')
as t1(
    acquirer_id integer,
    acquirer character varying(21)
);

--
--Migrate merchant_bank
--

INSERT INTO public.merchant_banks(merchant_id,bank_routing_number,bank_dda_number,bank_name,bank_routing_number_disp,bank_dda_number_disp,fees_routing_number,fees_dda_number,fees_routing_number_disp,fees_dda_number_disp)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_bank')
as t1 (
    merchant_id character varying(50),
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



DELETE FROM public.merchant_banks WHERE merchant_id = 'BECKER';
DELETE FROM public.merchant_banks WHERE merchant_id like '''%';
DELETE FROM public.merchant_banks where merchant_id LIKE 'TEST%';
DELETE FROM public.merchant_banks where merchant_id LIKE '%+%';
DELETE FROM public.merchant_banks where merchant_id LIKE '%-%';
DELETE FROM public.merchant_banks where merchant_id = '';
DELETE FROM public.merchant_banks where char_length(merchant_id) > 16;

--
-- Convert merchant_banks.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE merchant_banks ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Cleanup merchant_id column
--

DELETE FROM public.merchant_banks where merchant_id in ('3948000031001073''''','''''764111004240274''''','''''
3948000031000615''''','''''3948000031000652''''','''''3948000031000778''''','''''3948000031000800''''','''''
3948000031001072''''','''''3948000031001243''''','''''3948000031001244''''','''''3948000031001273''''','''''
3948000031001460''''','''''3948000031001503''''','''''3948000031001529''''','''''3948000031001633''''','''''
3948000031001636''''','''''3948000031001645''''','''''3948000031001688''''','''''4541619740008148''''','''''
3948000030000051''''','''''3948000031003770''''','''''3948000000000000''''','''''3948906204001224''''','''''
764111004241020''''','''''764111004241054''''','''''3948906204001235''''','''''764111004240163'''',''''
7641110042401510'''',''''764111042402276');

--
-- Migrate merchant_bin
--

INSERT INTO public.merchant_bins (bin_id, bin)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_bin order by bin_id asc')
as t1 (
    bin_id integer,
    bin character varying(21)
);

--
-- Migrate merchant_cancellation
--

INSERT INTO public.merchant_cancellations (merchant_id,date_submitted,date_completed,fee_charged,reason,status,axia_invoice_number,date_inactive,merchant_cancellation_subreason,subreason_id)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_cancellation')
as t1 (
    merchant_id character varying(50),
    date_submitted date,
    date_completed date,
    fee_charged real,
    reason text,
    status character varying(5),
    axia_invoice_number bigint,
    date_inactive date,
    merchant_cancellation_subreason character varying(255),
    subreason_id integer
);



--
-- Convert merchant_cancellations.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE merchant_cancellations ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate merchant_cancellation_subreason
--

INSERT INTO public.merchant_cancellation_subreasons (old_id,name,visible)
SELECT * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_cancellation_subreason order by id ASC')
as t1 (
    id integer,
    name character varying(65),
    visible boolean
);

-- SELECT setval('merchant_cancellation_subreasons_id_seq', (SELECT id FROM axia_legacy. merchant_cancellation_subreasons ORDER BY id DESC LIMIT 1));

--
-- Migrate merchant_card_types
--

INSERT INTO public.merchant_card_types(merchant_id,card_type)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_card_types')
as t1 (
    merchant_id character varying(50),
    card_type character varying(36)
);

DELETE FROM public.merchant_card_types WHERE merchant_id = 'BECKER';
DELETE FROM public.merchant_card_types WHERE merchant_id like '''%';
DELETE FROM public.merchant_card_types where merchant_id LIKE 'TEST%';
DELETE FROM public.merchant_card_types where merchant_id LIKE '%+%';
DELETE FROM public.merchant_card_types where merchant_id LIKE '%-%';
DELETE FROM public.merchant_card_types where merchant_id = '';
DELETE FROM public.merchant_card_types where char_length(merchant_id) > 16;

--ALTER TABLE merchant_card_types ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate merchant_change
--

INSERT INTO public.merchant_changes(change_id,change_type_id,merchant_id,user_id,status,date_entered,time_entered,date_approved,time_approved,change_data,merchant_note_id,approved_by_user_id,programming_id)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_change')
as t1 (
    change_id integer,
    change_type integer,
    merchant_id character varying(50),
    user_id integer,
    status character varying(5),
    date_entered date,
    time_entered time without time zone,
    date_approved date,
    time_approved time without time zone,
    change_data text,
    merchant_note_id integer,
    approved_by integer,
    "programming_id" integer
);
DELETE FROM public.merchant_changes  where user_id='0';
DELETE FROM public.merchant_changes where merchant_id LIKE 'TEST%';
--ALTER TABLE merchant_changes DROP COLUMN "programming_id";
--ALTER TABLE merchant_changes ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

-- SELECT setval('merchant_changes_id_seq', (SELECT id FROM axia_legacy. merchant_changes ORDER BY id DESC LIMIT 1));

--
-- Migrate merchant_note
--

INSERT INTO public.merchant_notes (merchant_note_id,note_type_id,user_id,merchant_id,note_date,note,note_title,general_status,date_changed,critical,note_sent)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_note ORDER BY merchant_note_id ASC')
as t1 (
    merchant_note_id integer,
    note_type character varying(36),
    user_id integer,
    merchant_id character varying(50),
    note_date date,
    note text,
    note_title character varying(200),
    general_status character varying(5),
    date_changed date,
    critical integer,
    note_sent integer
);


DELETE FROM public.merchant_notes WHERE merchant_id = 'BECKER';
DELETE FROM public.merchant_notes WHERE merchant_id LIKE '''%';
DELETE FROM public.merchant_notes where merchant_id LIKE 'TEST%';
DELETE FROM public.merchant_notes where merchant_id LIKE '%+%';
DELETE FROM public.merchant_notes where merchant_id LIKE '%-%';
DELETE FROM public.merchant_notes where merchant_id LIKE '%FAT%';
DELETE FROM public.merchant_notes where merchant_id = '';
DELETE FROM public.merchant_notes where char_length(merchant_id) > 16;
DELETE FROM public.merchant_notes where merchant_note_id in ('71501','71429','71314','70708','70706','70705','70702','46854','2782','2781','2780','20');

--ALTER TABLE merchant_notes ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

-- SELECT setval('merchant_notes_id_seq', (SELECT id FROM axia_legacy. merchant_notes ORDER BY id DESC LIMIT 1));

--
-- Migrate merchant_owner
--

INSERT INTO public.merchant_owners (owner_id,merchant_id,owner_social_sec_no,owner_equity,owner_name,owner_title,owner_social_sec_no_disp)
--SELECT owner_id as id,owner_id,merchant_id,owner_social_sec_no,owner_equity,owner_name,owner_title,owner_social_sec_no_disp
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_owner ORDER BY owner_id ASC')
as t1 (
    owner_id integer,
    merchant_id character varying(50),
    owner_social_sec_no character varying(255),
    owner_equity integer,
    owner_name character varying(100),
    owner_title character varying(40),
    owner_social_sec_no_disp character varying(4)
);

DELETE FROM public.merchant_owners WHERE merchant_id = 'BECKER';
DELETE FROM public.merchant_owners WHERE merchant_id like '''%';
DELETE FROM public.merchant_owners where merchant_id LIKE 'TEST%';
DELETE FROM public.merchant_owners where merchant_id LIKE '%+%';
DELETE FROM public.merchant_owners where merchant_id LIKE '%-%';
DELETE FROM public.merchant_owners where merchant_id LIKE '%FAT%';
DELETE FROM public.merchant_owners where merchant_id = '';
DELETE FROM public.merchant_owners where char_length(merchant_id) > 16;
DELETE FROM public.merchant_owners where owner_id in ('546','1913','1950','2076','2098','2367','2368','2528','2529','2549','2729','2772','2798','2901','2904','2913','2956','3416','5418','8432','10073','10132','10133','10162','10207','10719','11406','11707');
--ALTER TABLE merchant_owners ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

-- SELECT setval('merchant_owners_id_seq', (SELECT id FROM axia_legacy. merchant_owners ORDER BY id DESC LIMIT 1));

--
-- Migrate merchant_pci
--

INSERT INTO public.merchant_pcis (merchant_id,compliance_level,saq_completed_date,compliance_fee,insurance_fee,last_security_scan,scanning_company)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_pci')
as t1 (
    merchant_id character varying(50),
    compliance_level integer,
    saq_completed_date date,
    compliance_fee double precision,
    insurance_fee double precision,
    last_security_scan date,
    scanning_company character varying(255)
);

--ALTER TABLE merchant_pcis ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- migrate merchant_reference
--

INSERT INTO public.merchant_references (merchant_ref_seq_number,merchant_id,merchant_ref_type,bank_name,person_name,phone)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_reference')
as t1 (
    merchant_ref_seq_number integer,
    merchant_id character varying(50),
    merchant_ref_type character varying(36),
    bank_name character varying(100),
    person_name character varying(100),
    phone character varying(20)
);

DELETE FROM public.merchant_references where merchant_id LIKE 'TEST%';

--ALTER TABLE merchant_references ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- migrate merchant_reference_type
--

INSERT INTO public.merchant_reference_types (merchant_ref_type,merchant_ref_type_desc)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_reference_type')
as t1 (
    merchant_ref_type character varying(36),
    merchant_ref_type_desc character varying(50)
);

INSERT INTO public.merchant_reject_lines (old_id, reject_id,fee,status_id,status_date,notes)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_reject_line')
as t1 (
        "id" varchar(36),
	"rejectid" integer,
	"fee" real,
	"statusid" integer,
	"status_date" date,
	"notes" text
);

INSERT INTO public.merchant_reject_statuses (old_id,name,collected,priority)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_reject_status')
as t1 (
	"id" varchar(36),
	"name" varchar(57),
	"collected" boolean,
	"priority" integer
);

INSERT INTO public.merchant_reject_recurrances (old_id,name)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_reject_recurrance')
as t1 (
	"id" varchar(36),        
	"name" varchar(57) 
);

INSERT INTO public.merchant_reject_types (old_id,name)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_reject_type')
as t1 (
	"id" varchar(36),
	"name" varchar(57)
);

--
-- migrate merchant_reference
--

INSERT INTO public.merchant_rejects (old_id,merchant_id,trace,reject_date,type_id,code,amount,recurrance_id,open,loss_axia,loss_mgr1,loss_mgr2,loss_rep)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. merchant_reject')
as t1 (
    id integer,
    merchant_id character varying(255),
    trace bigint,
    reject_date date,
    type_id integer,
    code character varying(9),
    amount numeric(8,2),
    recurrance_id integer,
    open boolean,
    loss_axia real,
    loss_mgr1 real,
    loss_mgr2 real,
    loss_rep real
);


--ALTER TABLE merchant_rejects ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
-- SELECT setval('merchant_rejects_id_seq', (SELECT id FROM axia_legacy. merchant_rejects ORDER BY id DESC LIMIT 1));



--
-- Migrate "users"
--

INSERT INTO public."users" (user_id,user_type_id,user_title,user_first_name,user_last_name,
username,password,user_email,user_phone,user_fax,user_admin,parent_user_id,
user_commission,inactive_date,last_login_date,last_login_ip,entity_id,active,
initials,manager_percentage,date_started,split_commissions,bet_extra_pct,
secondary_parent_user_id,manager_percentage_secondary,discover_bet_extra_pct)
SELECT user_id as user_id,user_type,user_title,user_first_name,user_last_name,
username,password,user_email,user_phone,user_fax,user_admin,parent_user,
user_commission,inactive_date,last_login_date,last_login_ip,entity,active,
initials,manager_percentage,date_started,split_commissions,bet_extra_pct,
parent_user_secondary,manager_percentage_secondary,discover_bet_extra_pct FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. "user" ORDER BY user_id ASC')
as t1(    
    user_id integer,
    user_type character varying(36),
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
    entity character varying(36),
    active integer,
    initials character varying(5),
    manager_percentage double precision,
    date_started date,
    split_commissions boolean,
    bet_extra_pct boolean,
    parent_user_secondary integer,
    manager_percentage_secondary double precision,
    discover_bet_extra_pct boolean
);

--ALTER TABLE users RENAME COLUMN user_type TO user_type_id;
--ALTER TABLE users ALTER COLUMN user_type_id type int using cast(user_type_id as int);
--
-- increment users_id_seq value to match last users.id
--

-- SELECT setval('users_id_seq', (SELECT id FROM axia_legacy. "users" ORDER BY id DESC LIMIT 1));

--
-- Migrate saq_control_scan
--

INSERT INTO public.saq_control_scans (merchant_id,saq_type,first_scan_date,first_questionnaire_date,scan_status,questionnaire_status,creation_date,dba,quarterly_scan_fee,sua,pci_compliance,offline_compliance,compliant_date,host_count,submids)
SELECT * FROM axia_legacy. dblink('pg84', 'SELECT * FROM axia_legacy. saq_control_scan')
as t1 (
    merchant_id character varying,
    saq_type character varying(4),
    first_scan_date date,
    first_questionnaire_date date,
    scan_status character varying,
    questionnaire_status character varying,
    creation_date date,
    dba character varying(80),
    quarterly_scan_fee double precision,
    sua timestamp with time zone,
    pci_compliance character varying(3),
    offline_compliance character varying(3),
    compliant_date date,
    host_count integer,
    submids text
);


--ALTER TABLE saq_control_scans ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate merchant_uw_final_approveds
--
INSERT INTO public.merchant_uw_final_approveds (merchant_uw_final_approved_id,name) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_uw_final_approved')
as t1(
    id integer,
    name character varying(21)
);

-- SELECT setval ('merchant_uw_final_approved_id_seq', (select id FROM axia_legacy. merchant_uw_final_approved order by id desc limit 1));

--
-- Migrate merchant_uw_final_statuses
--
INSERT INTO public.merchant_uw_final_statuses (old_id,name) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_uw_final_status')
as t1(
    id integer,
    name character varying(21)
);

-- SELECT setval ('merchant_uw_final_statuses_id_seq', (select id FROM axia_legacy. merchant_uw_final_statuses order by id desc limit 1));

--
-- Migrate merchant_uws
--
INSERT INTO public.merchant_uws (merchant_id,tier_assignment,credit_pct,chargeback_pct,final_status_id,final_approved_id,final_date,final_notes,mcc,expedited) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. merchant_uw')
as t1(
    merchant_id character varying(255),
    tier_assignment integer,
    credit_pct real,
    chargeback_pct real,
    final_status_id integer,
    final_approved_id integer,
    final_date date,
    final_notes text,
    mcc character varying(255),
    expedited boolean
);

DELETE FROM public.merchant_uws WHERE 
merchant_id = 'BECKER' 
OR merchant_id like '''%' 
OR merchant_id LIKE 'TEST%'
OR merchant_id LIKE '%+%'
OR merchant_id LIKE '%-%'
OR merchant_id LIKE '%FAT%'
OR merchant_id = ''
OR char_length(merchant_id) > 16;
--
-- Convert merchant_uws.merchant_id FROM axia_legacy. varchar to integer
--
--ALTER TABLE merchant_uws ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate onlineapp_email_timeline_subjects
--
INSERT INTO public.onlineapp_email_timeline_subjects (old_id,subject) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_email_timeline_subjects')
as t1(
    id integer,
    subject character varying(40)
);

-- SELECT setval ('onlineapp_email_timeline_subjects_id_seq', select max(id) FROM axia_legacy.onlineapp_email_timeline_subjects);

--
-- Migrate onlineapp_applications
--
INSERT INTO public.onlineapp_applications (old_id,user_id,status,hash,rs_document_guid,ownership_type,legal_business_name,mailing_address,mailing_city,mailing_state,mailing_zip,mailing_phone,mailing_fax,federal_taxid,corp_contact_name,corp_contact_name_title,corporate_email,loc_same_as_corp,dba_business_name,location_address,location_city,location_state,location_zip,location_phone,location_fax,customer_svc_phone,loc_contact_name,loc_contact_name_title,location_email,website,bus_open_date,length_current_ownership,existing_axia_merchant,current_mid_number,general_comments,location_type,location_type_other,merchant_status,landlord_name,landlord_phone,business_type,products_services_sold,return_policy,days_until_prod_delivery,monthly_volume,average_ticket,highest_ticket,current_processor,card_present_swiped,card_present_imprint,card_not_present_keyed,card_not_present_internet,method_total,direct_to_customer,direct_to_business,direct_to_govt,products_total,high_volume_january,high_volume_february,high_volume_march,high_volume_april,high_volume_may,high_volume_june,high_volume_july,high_volume_august,high_volume_september,high_volume_october,high_volume_november,high_volume_december,moto_storefront_location,moto_orders_at_location,moto_inventory_housed,moto_outsourced_customer_service,moto_outsourced_shipment,moto_outsourced_returns,moto_outsourced_billing,moto_sales_methods,moto_billing_monthly,moto_billing_quarterly,moto_billing_semiannually,moto_billing_annually,moto_policy_full_up_front,moto_policy_days_until_delivery,moto_policy_partial_up_front,moto_policy_partial_with,moto_policy_days_until_final,moto_policy_after,bank_name,bank_contact_name,bank_phone,bank_address,bank_city,bank_state,bank_zip,depository_routing_number,depository_account_number,same_as_depository,fees_routing_number,fees_account_number,trade1_business_name,trade1_contact_person,trade1_phone,trade1_acct_num,trade1_city,trade1_state,trade2_business_name,trade2_contact_person,trade2_phone,trade2_acct_num,trade2_city,trade2_state,currently_accept_amex,existing_se_num,want_to_accept_amex,want_to_accept_discover,term1_quantity,term1_type,term1_provider,term1_use_autoclose,term1_what_time,term1_programming_avs,term1_programming_server_nums,term1_programming_tips,term1_programming_invoice_num,term1_programming_purchasing_cards,term1_accept_debit,term1_pin_pad_type,term1_pin_pad_qty,term2_quantity,term2_type,term2_provider,term2_use_autoclose,term2_what_time,term2_programming_avs,term2_programming_server_nums,term2_programming_tips,term2_programming_invoice_num,term2_programming_purchasing_cards,term2_accept_debit,term2_pin_pad_type,term2_pin_pad_qty,owner1_percentage,owner1_fullname,owner1_title,owner1_address,owner1_city,owner1_state,owner1_zip,owner1_phone,owner1_fax,owner1_email,owner1_ssn,owner1_dob,owner2_percentage,owner2_fullname,owner2_title,owner2_address,owner2_city,owner2_state,owner2_zip,owner2_phone,owner2_fax,owner2_email,owner2_ssn,owner2_dob,referral1_business,referral1_owner_officer,referral1_phone,referral2_business,referral2_owner_officer,referral2_phone,referral3_business,referral3_owner_officer,referral3_phone,rep_contractor_name,fees_rate_discount,fees_rate_structure,fees_qualification_exemptions,fees_startup_application,fees_auth_transaction,fees_monthly_statement,fees_misc_annual_file,fees_startup_equipment,fees_auth_amex,fees_monthly_minimum,fees_misc_chargeback,fees_startup_expedite,fees_auth_aru_voice,fees_monthly_debit_access,fees_startup_reprogramming,fees_auth_wireless,fees_monthly_ebt,fees_startup_training,fees_monthly_gateway_access,fees_startup_wireless_activation,fees_monthly_wireless_access,fees_startup_tax,fees_startup_total,fees_pin_debit_auth,fees_ebt_discount,fees_pin_debit_discount,fees_ebt_auth,rep_discount_paid,rep_amex_discount_rate,rep_business_legitimate,rep_photo_included,rep_inventory_sufficient,rep_goods_delivered,rep_bus_open_operating,rep_visa_mc_decals_visible,rep_mail_tel_activity,created,modified,moto_inventory_owned,moto_outsourced_customer_service_field,moto_outsourced_shipment_field,moto_outsourced_returns_field,moto_sales_local,moto_sales_national,site_survey_signature,api,var_status,install_var_rs_document_guid,tickler_id,callback_url,guid,redirect_url)
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_applications')
as t1(
    id integer,
    user_id integer,
    status character varying(36),
    hash character varying(32),
    rs_document_guid character varying(40),
    ownership_type character varying(255),
    legal_business_name character varying(255),
    mailing_address character varying(255),
    mailing_city character varying(255),
    mailing_state character varying(255),
    mailing_zip character varying(36),
    mailing_phone character varying(20),
    mailing_fax character varying(20),
    federal_taxid character varying(255),
    corp_contact_name character varying(255),
    corp_contact_name_title character varying(50),
    corporate_email character varying(255),
    loc_same_as_corp boolean,
    dba_business_name character varying(255),
    location_address character varying(255),
    location_city character varying(255),
    location_state character varying(255),
    location_zip character varying(36),
    location_phone character varying(20),
    location_fax character varying(20),
    customer_svc_phone character varying(20),
    loc_contact_name character varying(255),
    loc_contact_name_title character varying(255),
    location_email character varying(255),
    website character varying(255),
    bus_open_date character varying(255),
    length_current_ownership character varying(255),
    existing_axia_merchant character varying(36),
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
    moto_storefront_location character varying(36),
    moto_orders_at_location character varying(36),
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
    moto_policy_full_up_front character varying(36),
    moto_policy_days_until_delivery character varying(36),
    moto_policy_partial_up_front character varying(36),
    moto_policy_partial_with character varying(36),
    moto_policy_days_until_final character varying(36),
    moto_policy_after character varying(36),
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
    currently_accept_amex character varying(36),
    existing_se_num character varying(255),
    want_to_accept_amex character varying(36),
    want_to_accept_discover character varying(36),
    term1_quantity smallint,
    term1_type character varying(30),
    term1_provider character varying(255),
    term1_use_autoclose character varying(36),
    term1_what_time character varying(255),
    term1_programming_avs boolean,
    term1_programming_server_nums boolean,
    term1_programming_tips boolean,
    term1_programming_invoice_num boolean,
    term1_programming_purchasing_cards boolean,
    term1_accept_debit character varying(36),
    term1_pin_pad_type character varying(255),
    term1_pin_pad_qty smallint,
    term2_quantity smallint,
    term2_type character varying(30),
    term2_provider character varying(255),
    term2_use_autoclose character varying(36),
    term2_what_time character varying(255),
    term2_programming_avs boolean,
    term2_programming_server_nums boolean,
    term2_programming_tips boolean,
    term2_programming_invoice_num boolean,
    term2_programming_purchasing_cards boolean,
    term2_accept_debit character varying(36),
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
    rep_discount_paid character varying(36),
    rep_amex_discount_rate character varying(20),
    rep_business_legitimate character varying(36),
    rep_photo_included character varying(36),
    rep_inventory_sufficient character varying(36),
    rep_goods_delivered character varying(36),
    rep_bus_open_operating character varying(36),
    rep_visa_mc_decals_visible character varying(36),
    rep_mail_tel_activity character varying(36),
    created timestamp(6) without time zone,
    modified timestamp(6) without time zone,
    moto_inventory_owned character varying(36),
    moto_outsourced_customer_service_field character varying(40),
    moto_outsourced_shipment_field character varying(40),
    moto_outsourced_returns_field character varying(40),
    moto_sales_local boolean,
    moto_sales_national boolean,
    site_survey_signature character varying(40),
    api integer,
    var_status character varying(36),
    install_var_rs_document_guid character varying(32),
    tickler_id varchar(36),
    callback_url varchar(255),
    guid character varying(40),
    redirect_url character varying(255)
);

-- SELECT setval ('onlineapp_applications_id_seq', (select id FROM axia_legacy. onlineapp_applications order by id desc limit 1));


--
-- Migrate onlineapp_apips
--
INSERT INTO public.onlineapp_api_logs (user_id,user_token,ip_address,request_string,request_url,request_type,created,auth_status) 
select user_id,user_token,ip_address,request_string,request_url,request_type,created,auth_status FROM axia_legacy. dblink('pg84', 'select user_id,user_token,ip_address,request_string,request_url,request_type,created,auth_status FROM axia_legacy. onlineapp_api_logs')
as t1(
        "user_id" integer,
        "user_token" character varying(40),
        "ip_address" inet,
        "request_string" text,
        "request_url" text,
        "request_type" character varying(10),
        "created" timestamp without time zone,
	"auth_status" character varying(7) 
);

--
-- Migrate onlineapp_apips
--
INSERT INTO public.onlineapp_apips (old_id,user_id,ip_address) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_apips')
as t1(
    id int,
    user_id integer,
    ip_address inet
);

-- SELECT setval ('onlineapp_apips_id_seq', (select id FROM axia_legacy. onlineapp_apips order by id desc limit 1));

INSERT INTO public.note_types VALUES ('a19ffa61-d9dd-486b-a8f0-1d1ef5f44dd2', 'CHG', 'Change Request');
INSERT INTO public.note_types VALUES ('0bfee249-5c37-417c-aec7-83dcd2b2f566', 'GNR', 'General Note');
INSERT INTO public.note_types VALUES ('083e3d46-76f2-42d9-ad7b-6fb3a9775b3c', 'PRG', 'Programming Note');
INSERT INTO public.note_types VALUES ('85b32624-aca2-44f2-9924-49cddc6b2e5a', 'INS', 'Installation & Setup Note');

--
-- Migrate networks
--
INSERT INTO public.networks (network_id,network_description) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. network')
as t1(
    network_id integer,
    network_description character varying(50)
);


INSERT INTO public.onlineapp_coversheets (old_id, onlineapp_application_id, user_id, status, setup_existing_merchant, setup_banking, setup_statements, setup_drivers_license, setup_new_merchant, setup_business_license, setup_other, setup_field_other, setup_tier_select, setup_tier3, setup_tier4, setup_tier5_financials, setup_tier5_processing_statements, setup_tier5_bank_statements, setup_equipment_terminal, setup_equipment_gateway, setup_install, setup_starterkit, setup_equipment_payment, setup_lease_price, setup_lease_months, setup_debit_volume, setup_item_count, setup_referrer, setup_referrer_type, setup_referrer_pct, setup_reseller, setup_reseller_type, setup_reseller_pct, setup_notes, cp_encrypted_sn, cp_pinpad_ra_attached, cp_giftcards, cp_check_guarantee, cp_check_guarantee_info, cp_pos, cp_pos_contact, micros, micros_billing, gateway_option, gateway_package, gateway_gold_subpackage, gateway_epay, gateway_billing, moto_online_chd, moto_developer, moto_company, moto_gateway, moto_contact, moto_phone, moto_email, created, modified, cobranded_application_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_coversheets')
as t1(
    id varchar(36),    
    onlineapp_application_id integer,
    user_id integer,
    status character varying(10),
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


INSERT INTO public.onlineapp_multipasses (merchant_id,device_number,username,pass,in_use,application_id,created,modified) 
select merchant_id,device_number,username,pass,in_use,application_id,created,modified 
FROM axia_legacy. dblink('pg84', 'select merchant_id,device_number,username,pass,in_use,application_id,created,modified FROM axia_legacy. onlineapp_multipasses ORDER BY id ASC')
as t1(
    merchant_id character varying(16),
    device_number character varying(14),
    username character varying(20),
    pass character varying(20),
    in_use boolean,
    application_id integer,
    created timestamp without time zone,
    modified timestamp without time zone
);

--
-- Migrate onlineapp_email_timelines
--
-- ALTER TABLE onlineapp_email_timelines RENAME COLUMN app_id TO onlineapp_application_id;

INSERT INTO public.onlineapp_email_timelines (app_id,date,email_timeline_subject_id,recipient,cobranded_application_id) 
select app_id,date,email_timeline_subject_id,recipient,cobranded_application_id FROM axia_legacy. dblink('pg84', 'select app_id,date,email_timeline_subject_id,recipient,cobranded_application_id FROM axia_legacy. onlineapp_email_timelines ORDER BY id ASC')
as t1(
    
    app_id integer,
    date timestamp without time zone,
    email_timeline_subject_id integer,
    recipient character varying(50),
    cobranded_application_id integer
);

-- SELECT setval ('onlineapp_email_timelines_id_seq', (select id FROM axia_legacy. onlineapp_email_timelines order by id desc limit 1));

--
-- Migrate onlineapp_epayments
--
INSERT INTO public.onlineapp_epayments (id,pin,application_id,merchant_id,user_id,onlineapp_application_id,date_boarded,date_retrieved) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_epayments')
as t1(
    id integer,
    pin integer,
    application_id integer,
    merchant_id character varying(40),
    user_id integer,
    onlineapp_application_id integer,
    date_boarded timestamp without time zone,
    date_retrieved timestamp without time zone
);

-- SELECT setval ('onlineapp_epayments_id_seq', (select id FROM axia_legacy. onlineapp_epayments order by id desc limit 1));
--ALTER TABLE onlineapp_epayments ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
--
-- Migrate onlineapp_groups
--
INSERT INTO public.onlineapp_groups (old_id,name,created,modified) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_groups ORDER BY id ASC')
as t1(
    id integer,
    name character varying(100),
    created timestamp(6) without time zone,
    modified timestamp(6) without time zone
);

-- SELECT setval ('onlineapp_groups_id_seq', (select id FROM axia_legacy. onlineapp_groups order by id desc limit 1));

--
-- Migrate onlineapp_settings
--
INSERT INTO public.onlineapp_settings (key,value,description) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_settings')
as t1(
    key character varying(255),
    value character varying(255),
    description text
);

--
-- Migrate onlineapp_users
--
INSERT INTO public.onlineapp_users (user_id,email,password,group_id,created,modified,token,token_used,token_uses,firstname,lastname,extension, active,api_password,api_enabled,cobrand_id,template_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. onlineapp_users')
as t1(
    id integer,
    email character varying(255),
    password character varying(40),
    group_id integer,
    created timestamp(6) without time zone,
    modified timestamp(6) without time zone,
    token character(40),
    token_used timestamp without time zone,
    token_uses integer,
    firstname character(40),
    lastname character(40),
    extension integer,
    active boolean,
    api_password character varying(50),
    api_enabled boolean,
    cobrande_id integer,
    template_id integer
);

-- SELECT setval ('onlineapp_users_id_seq', (select id FROM axia_legacy. onlineapp_users order by id desc limit 1));

--
-- Migrate orderitem_types
--
INSERT INTO public.orderitem_types (orderitem_type_id,orderitem_type_description) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. orderitem_type')
as t1(
    orderitem_type_id integer,
    orderitem_type_description character varying(55)
);

--
-- Migrate orderitems
--
INSERT INTO public.orderitems (orderitem_id,order_id,equipment_type_id,equipment_item_description,quantity,equipment_item_true_price,
equipment_item_rep_price,equipment_item_id,hardware_sn,hardware_replacement_for,warranty,item_merchant_id,item_tax,item_ship_type,
item_ship_cost,item_commission_month,item_ach_seq,item_date_ordered,type_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. orderitems')
as t1(
    orderitem_id integer,
    order_id integer,
    equipment_type character varying(36),
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

--ALTER TABLE orderitems ALTER COLUMN item_merchant_id TYPE bigint USING CAST(item_merchant_id AS bigint);
--
-- Migrate orderitems_replacements
--
INSERT INTO public.orderitems_replacements (orderitem_replacement_id,orderitem_id,shipping_axia_to_merchant_id,
shipping_axia_to_merchant_cost,shipping_merchant_to_vendor_id,shipping_merchant_to_vendor_cos,shipping_vendor_to_axia_id,
shipping_vendor_to_axia_cost,ra_num,tracking_num_old,date_shipped_to_vendor,date_arrived_from_vendor,amount_billed_to_merchant,tracking_num) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. orderitems_replacement')
as t1(
    orderitem_replacement_id integer,
    orderitem_id integer,
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

--
-- Migrate orders
--
INSERT INTO public.orders (order_id,status,user_id,date_ordered,date_paid,merchant_id,shipping_cost,tax,ship_to,tracking_number,notes,invoice_number,shipping_type,display_id,ach_seq_number,commission_month,vendor_id,add_item_tax,commission_month_nocharge) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. orders')
as t1(
    order_id integer,
    status character varying(5),
    user_id integer,
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

DELETE FROM public.orders WHERE 
merchant_id = 'BECKER' 
OR merchant_id like '''%' 
OR merchant_id LIKE 'TEST%'
OR merchant_id LIKE '%+%'
OR merchant_id LIKE '%-%'
OR merchant_id LIKE '%FAT%'
OR merchant_id = ''
OR char_length(merchant_id) > 16;
--
-- Convert orders.merchant_id FROM axia_legacy. varchar to integer
--
--ALTER TABLE orders ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate partners
--
INSERT INTO public.partners (partner_old_id,partner_name,active) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. partner')
as t1(
    partner_id integer,
    partner_name character varying(255),
    active integer
);

--
-- Migrate pci_billing_histories
--
INSERT INTO public.pci_billing_histories (old_id,pci_billing_type_id,saq_merchant_id,billing_date,date_change,operation) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. pci_billing_history')
as t1(
    id integer,
    pci_billing_type_id integer,
    saq_merchant_id integer,
    billing_date date,
    date_change date,
    operation character varying(25)
);

-- SELECT setval ('pci_billing_histories_id_seq', (select id FROM axia_legacy. pci_billing_histories order by id desc limit 1));

--
-- Migrate pci_compliance_date_types
--
INSERT INTO public.pci_compliance_date_types (old_id,name) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. pci_compliance_date_type')
as t1(
    id integer,
    name character varying(255)
);

-- SELECT setval ('pci_compliance_date_types_id_seq', (select id FROM axia_legacy. pci_compliance_date_types order by id desc limit 1));

--
-- Migrate pci_compliance_status_logs
--
INSERT INTO public.pci_compliance_status_logs (pci_compliance_date_type_id,saq_merchant_id,date_complete,date_change,operation) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. pci_compliance_status_log')
as t1(
    pci_compliance_date_type_id integer,
    saq_merchant_id integer,
    date_complete date,
    date_change timestamp without time zone,
    operation character varying(25)
);

--
-- Migrate pci_compliances
--
INSERT INTO public.pci_compliances (pci_compliance_date_type_id,saq_merchant_id,date_complete) 
select pci_compliance_date_type_id,saq_merchant_id,date_complete 
FROM axia_legacy. dblink('pg84', 'select pci_compliance_date_type_id,saq_merchant_id,date_complete FROM axia_legacy. pci_compliance')
as t1(
    pci_compliance_date_type_id integer,
    saq_merchant_id integer,
    date_complete date
);

-- SELECT setval ('pci_compliances_id_seq', (select id FROM axia_legacy. pci_compliances order by id desc limit 1));

--
-- Migrate permission_groups
--
-- INSERT INTO public.permission_groups (permission_group,permission_group_description) 
-- select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. permission_group')
-- as t1(
--     permission_group character varying(20),
--     permission_group_description character varying(50)
-- );

--
-- Migrate permissions
--
-- INSERT INTO public.permissions (permission_id,permission_group,permission_description) 
-- select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. permission')
-- as t1(
--     permission_id integer,
--     permission_group character varying(20),
--     permission_description character varying(100)
-- );

--
-- Migrate pricing_matrices
--
INSERT INTO public.pricing_matrices (matrix_id,user_id,user_type_id,matrix_profit_perc,matrix_new_monthly_volume,matrix_new_monthly_profit,matrix_view_conjunction,matrix_total_volume,matrix_total_accounts,matrix_total_profit,matrix_draw,matrix_new_accounts_min,matrix_new_accounts_max) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. pricing_matrix')
as t1(
    matrix_id integer,
    user_id integer,
    user_type_id character varying(36),
    matrix_profit_perc real,
    matrix_new_monthly_volume real,
    matrix_new_monthly_profit real,
    matrix_view_conjunction character varying(36),
    matrix_total_volume real,
    matrix_total_accounts real,
    matrix_total_profit real,
    matrix_draw real,
    matrix_new_accounts_min real,
    matrix_new_accounts_max real
);

--
-- Migrate products_and_services
--
INSERT INTO public.products_and_services (merchant_id,products_services_type_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. products_and_services')
as t1(
    merchant_id character varying(50),
    products_services_type character varying(36)
);

DELETE FROM public.products_and_services WHERE 
merchant_id = 'BECKER' 
OR merchant_id like '''%' 
OR merchant_id LIKE 'TEST%'
OR merchant_id LIKE '%+%'
OR merchant_id LIKE '%-%'
OR merchant_id LIKE '%FAT%'
OR merchant_id = ''
OR char_length(merchant_id) > 16;
--
-- Convert products_and_services.merchant_id FROM axia_legacy. varchar to integer
--
--ALTER TABLE products_and_services ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate products_services_types
--
INSERT INTO public.products_services_types (products_services_type_old_id,products_services_description, products_services_rppp) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. products_services_type')
as t1(    
    products_services_type character varying(36),
    products_services_description character varying(50),
    products_services_rppp boolean
);

--
-- Migrate referer_products_services_xrefs
--
INSERT INTO public.referer_products_services_xrefs (ref_seq_number,products_services_type_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. referer_products_services_xref')
as t1(    
    ref_seq_number integer,
    products_services_type character varying(36)
);

--
-- Migrate referers
--
INSERT INTO public.referers (ref_seq_number,ref_name,ref_ref_perc,active,ref_username,ref_password,ref_residuals,ref_commissions,is_referer) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. referers')
as t1(    
    ref_seq_number integer,
    ref_name character varying(50),
    ref_ref_perc real,
    active integer,
    ref_username character varying(50),
    ref_password character varying(50),
    ref_residuals boolean,
    ref_commissions boolean,
    is_referer boolean
);

--
-- Migrate referers_bets
--
INSERT INTO public.referers_bets (referers_bet_id,ref_seq_number,bet_code,pct) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. referers_bet')
as t1(    
    referers_bet_id integer,
    ref_seq_number integer,
    bet_code character varying(50),
    pct real
);

--
-- Migrate rep_cost_structures
--
INSERT INTO public.rep_cost_structures (user_id,debit_monthly_fee,debit_per_item_fee,gift_statement_fee,gift_magstripe_item_fee,
gift_magstripe_loyalty_fee,gift_chipcard_item_fee,gift_chipcard_loyalty_fee,gift_chipcard_onerate_fee,cg_volume,
vc_web_based_rate,vc_web_based_pi,vc_monthly_fee,vc_gateway_fee,ach_merchant_based,ach_file_fee,ach_eft_ccd_nw,ach_eft_ccd_w,
ach_eft_ppd_nw,ach_eft_ppd_w,ach_eft_rck,ach_reject_fee,ach_statement_fee,ach_secure_gateway_eft,ebt_per_item_fee,
ebt_monthly_fee,main_statement_fee,main_vrt_term_gwy_fee,vc_web_based_monthly_fee,vc_web_based_gateway_fee,te_transaction_fee,
af_annual_fee,multiple,auth_monthly_fee,auth_per_item_fee,debit_rate_rep_cost_pct,ach_rate,gw_rate,gw_per_item,
debit_per_item_fee_tsys,debit_rate_rep_cost_pct_tsys,debit_per_item_fee_tsys_new,debit_rate_rep_cost_pct_tsys_new,
discover_statement_fee,debit_per_item_fee_directconnect,debit_rate_rep_cost_pct_directconnect,debit_per_item_fee_sagepti,
debit_rate_rep_cost_pct_sagepti,tg_rate,tg_per_item,wp_rate,wp_per_item,tg_statement_fee) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. rep_cost_structure')
as t1(    
    user_id integer,
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

INSERT INTO public.rep_product_profit_pcts (user_id,products_services_type_id,pct,multiple,pct_gross,do_not_display) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. rep_product_profit_pct')
as t1( 
        user_id varchar(36),
        products_services_type_id varchar(36),
        pct real,
        multiple real,
        pct_gross real,
        do_not_display boolean
);

--
-- Migrate rep_partner_xrefs
--
INSERT INTO public.rep_partner_xrefs (user_id,partner_id,mgr1_id,mgr2_id,profit_pct,multiple,mgr1_profit_pct,mgr2_profit_pct) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. rep_partner_xref')
as t1(    
    user_id integer,
    partner_id integer,
    mgr1_id integer,
    mgr2_id integer,
    profit_pct real,
    multiple numeric(4,2),
    mgr1_profit_pct real,
    mgr2_profit_pct real
);

--
-- Migrate residual_pricings
--
INSERT INTO public.residual_pricings (
merchant_id,products_services_type_id,user_id,r_network,r_month,r_year,r_rate_pct,r_per_item_fee,r_statement_fee,m_rate_pct,
m_per_item_fee,m_statement_fee,refer_profit_pct,ref_seq_number,m_hidden_per_item_fee,m_hidden_per_item_fee_cross,
m_eft_secure_flag,m_gc_plan,m_pos_partner_flag,bet_code,bet_extra_pct,pct_volume,res_profit_pct,res_seq_number,
m_micros_ip_flag,m_micros_dialup_flag,m_micros_per_item_fee,m_wireless_flag,m_wireless_terminals,r_usaepay_flag,
r_epay_retail_flag,r_usaepay_gtwy_cost,r_usaepay_gtwy_add_cost,m_tgate_flag,m_petro_flag,ref_p_type,ref_p_value,
res_p_type,res_p_value,r_risk_assessment,ref_p_pct,res_p_pct
) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. residual_pricing')
as t1(
    merchant_id character varying(50),
    products_services_type character varying(36),
    user_id integer,
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
    pct_volume boolean,
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

DELETE FROM public.residual_pricings WHERE 
merchant_id = 'BECKER' 
OR merchant_id like '''%' 
OR merchant_id LIKE 'TEST%'
OR merchant_id LIKE '%+%'
OR merchant_id LIKE '%-%'
OR merchant_id LIKE '%FAT%'
OR merchant_id = ''
OR char_length(merchant_id) > 16;

--
-- Convert residual_pricings.merchant_id FROM axia_legacy. varchar to integer
--
--ALTER TABLE residual_pricings ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);



--
-- Migrate residual_reports
--
INSERT INTO public.residual_reports (merchant_id,products_services_type_id,user_id,r_network,r_month,r_year,r_rate_pct,r_per_item_fee,r_statement_fee,r_profit_pct,
r_profit_amount,m_rate_pct,m_per_item_fee,refer_profit_pct,refer_profit_amount,
ref_seq_number,status,m_statement_fee,r_avg_ticket,r_items,r_volume,total_profit,manager_id,manager_profit_pct,manager_profit_amount,bet_code,bet_extra_pct,
res_profit_pct,res_profit_amount,res_seq_number,manager_id_secondary,manager_profit_pct_secondary,manager_profit_amount_secondary,ref_p_type,ref_p_value,
res_p_type,res_p_value,ref_p_pct,res_p_pct,partner_id,partner_exclude_volume) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. residual_report')
as t1(
    merchant_id character varying(50),
    products_services_type character varying(36),
    user_id integer,
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

--
-- Convert residual_reports.merchant_id FROM axia_legacy. varchar to integer
--
--ALTER TABLE residual_reports ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate sales_goal_archives
--
INSERT INTO public.sales_goal_archives (user_id,goal_month,goal_year,goal_accounts,goal_volume,goal_profits,actual_accounts,actual_volume,actual_profits,goal_statements,goal_calls,actual_statements,actual_calls) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. sales_goal_archive')
as t1(    
    user_id integer,
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

--
-- Migrate sales_goals
--
INSERT INTO public.sales_goals (user_id,goal_accounts,goal_volume,goal_profits,goal_statements,goal_calls,goal_month) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. sales_goal')
as t1(    
    user_id integer,
    goal_accounts double precision,
    goal_volume double precision,
    goal_profits double precision,
    goal_statements double precision,
    goal_calls double precision,
    goal_month integer
);

--
-- Migrate saq_answers
--
INSERT INTO public.saq_answers (old_id,saq_merchant_survey_xref_id,saq_survey_question_xref_id,answer,date) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_answer')
as t1(
    id integer,
    saq_merchant_survey_xref_id integer,
    saq_survey_question_xref_id integer,
    answer boolean,
    date timestamp without time zone
);

-- SELECT setval ('saq_answers_id_seq', (select id FROM axia_legacy. saq_answers order by id desc limit 1));

--
-- Migrate saq_control_scan_unboardeds
--
INSERT INTO public.saq_control_scan_unboardeds (old_id,merchant_id,date_unboarded) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_control_scan_unboarded')
as t1(
    id integer,
    merchant_id character varying(50),
    date_unboarded date
);

--
-- Convert saq_control_scan_unboardeds.merchant_id FROM axia_legacy. varchar to integer
--
--ALTER TABLE saq_control_scan_unboardeds ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
-- SELECT setval ('saq_control_scan_unboardeds_id_seq', (select id FROM axia_legacy. saq_control_scan_unboardeds order by id desc limit 1));

--
-- Migrate saq_merchant_pci_email_sents
--
INSERT INTO public.saq_merchant_pci_email_sents (old_id,saq_merchant_id,saq_merchant_pci_email_id,date_sent) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_merchant_pci_email_sent')
as t1(
    id integer,
    saq_merchant_id integer,
    saq_merchant_pci_email_id integer,
    date_sent timestamp without time zone
);


-- SELECT setval ('saq_merchant_pci_email_sents_id_seq', (select id FROM axia_legacy. saq_merchant_pci_email_sents order by id desc limit 1));

--
-- Migrate saq_merchant_pci_emails
--
-- INSERT INTO public.saq_merchant_pci_emails (old_id,priority,interval,title,filename_prefix,visible) 
-- select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_merchant_pci_email')
-- as t1(
--     id int,
--     priority integer,
--     "interval" integer,
--     title character varying(255),
--     filename_prefix character varying(255),
--     visible boolean
-- );
INSERT INTO public.saq_merchant_pci_emails VALUES ('6e8d9040-e4e3-4a9a-8fbd-f510602bc7d0', 6, 6, 1, 'Subject: CORRECTION: An Important Reminder Regarding Mandatory PCI Compliance and the PCI-DSS Self-Assessment Questionnaire', 'pci_apology', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('5368a5b5-98d3-4263-b625-08f719f28f5e', 7, 7, 1, 'QUARTERLY REMINDER: An Important Reminder Regarding Your Quarterly PCI-DSS Vulnerability Scan', 'pci_email_7', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('11bb3864-6009-49c3-a1e5-b6fd9294068e', 5, 5, 30, 'NOTICE: An Important Reminder Regarding Mandatory PCI Compliance and the PCI-DSS Self-Assessment Questionnaire (SAQ)', 'pci_email_5', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('90170996-5681-4ee2-9b7e-219f06c0e9b0', 1, 1, 7, 'Welcome to Axia. An Introductory Email & Communication Regarding Mandatory PCI Compliance and the PCI-DSS Self-Assessment Questionnaire (SAQ)', 'pci_email_1', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('1e8b0888-a7d8-4a99-bd36-7a0841c568eb', 2, 2, 15, 'SECOND NOTICE: An Important Reminder Regarding Mandatory PCI Compliance and the PCI-DSS Self-Assessment Questionnaire (SAQ)', 'pci_email_2', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('daa48863-cf8b-425b-8bca-03bafe9ceb2b', 3, 3, 15, 'THIRD NOTICE: An Important Reminder Regarding Mandatory PCI Compliance and the PCI-DSS Self-Assessment Questionnaire (SAQ)', 'pci_email_3', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('d27454cb-da68-409b-8784-b1ac561655ef', 4, 4, 15, 'FOURTH NOTICE: An Important Reminder Regarding Mandatory PCI Compliance and the PCI-DSS Self-Assessment Questionnaire (SAQ)', 'pci_email_4', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('8c773d51-feee-4bc2-afe5-80b1c08edf9a', 8, 8, 10, 'QUARTERLY REMINDER: An Important Reminder Regarding Your Quarterly PCI-DSS Vulnerability Scan', 'pci_email_8', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('ea4328ee-2334-4edd-ac7c-c12c212f144d', 9, 9, 1, 'ANNUAL REMINDER: An Important Reminder Regarding Your Annual PCI-DSS SAQ', 'pci_email_9', true);
INSERT INTO public.saq_merchant_pci_emails VALUES ('34662ac9-25a5-4de5-88b2-95a30b8cd2a1', 10, 10, 10, 'ANNUAL REMINDER: An Important Reminder Regarding Your Annual PCI-DSS SAQ', 'pci_email_10', true);


-- SELECT setval ('saq_merchant_pci_emails_id_seq', (select id FROM axia_legacy. saq_merchant_pci_emails order by id desc limit 1));

--
-- Migrate saq_merchant_survey_xrefs
--
INSERT INTO public.saq_merchant_survey_xrefs (old_id,saq_merchant_id,saq_survey_id,saq_eligibility_survey_id,saq_confirmation_survey_id,datestart,datecomplete,ip,acknowledgement_name,acknowledgement_title,acknowledgement_company,resolution) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_merchant_survey_xref')
as t1(
    id integer,
    saq_merchant_id integer,
    saq_survey_id integer,
    saq_eligibility_survey_id integer,
    saq_confirmation_survey_id integer,
    datestart timestamp without time zone,
    datecomplete timestamp without time zone,
    ip character varying(15),
    acknowledgement_name character varying(255),
    acknowledgement_title character varying(255),
    acknowledgement_company character varying(255),
    resolution text
);



-- SELECT setval ('saq_merchant_survey_xrefs_id_seq', (select id FROM axia_legacy. saq_merchant_survey_xrefs order by id desc limit 1));

--
-- Migrate saq_merchants
--
INSERT INTO public.saq_merchants (old_id,merchant_id,merchant_name,merchant_email,password,email_sent,billing_date,next_billing_date) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_merchant')
as t1(
    id integer,
    merchant_id character varying(50),
    merchant_name character varying(255),
    merchant_email character varying(100),
    password character varying(255),
    email_sent timestamp without time zone,
    billing_date timestamp without time zone,
    next_billing_date timestamp without time zone
);

--
-- Convert saq_merchants.merchant_id FROM axia_legacy. varchar to integer
--
--ALTER TABLE saq_merchants ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);
-- SELECT setval ('saq_merchants_id_seq', (select id FROM axia_legacy. saq_merchants order by id desc limit 1));

--
-- Migrate saq_prequalifications
--
INSERT INTO public.saq_prequalifications (old_id,saq_merchant_id,result,date_completed,control_scan_code,control_scan_message) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_prequalification')
as t1(
    id integer,
    saq_merchant_id integer,
    result character varying(4),
    date_completed timestamp without time zone,
    control_scan_code integer,
    control_scan_message text
);

-- SELECT setval ('saq_prequalifications_id_seq', (select id FROM axia_legacy. saq_prequalifications order by id desc limit 1));

--
-- Migrate saq_saq_questions
--
INSERT INTO public.saq_questions (old_id,question) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_question')
as t1(
    id integer,
    question text
);

-- SELECT setval ('saq_questions_id_seq', (select id FROM axia_legacy. saq_questions order by id desc limit 1));

--
-- Migrate saq_survey_question_xrefs
--
INSERT INTO public.saq_survey_question_xrefs (old_id,saq_survey_id,saq_question_id,priority) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_survey_question_xref')
as t1(
    id integer,
    saq_survey_id integer,
    saq_question_id integer,
    priority integer
);

-- SELECT setval ('saq_survey_question_xrefs_id_seq', (select id FROM axia_legacy. saq_survey_question_xrefs order by id desc limit 1));

--
-- Migrate saq_surveys
--
INSERT INTO public.saq_surveys (old_id,name,saq_level,eligibility_survey_id,confirmation_survey_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. saq_survey')
as t1(
    id integer,
    name character varying(255),
    saq_level character varying(4),
    eligibility_survey_id integer,
    confirmation_survey_id integer
);

-- SELECT setval ('saq_surveys_id_seq', (select id FROM axia_legacy. saq_surveys order by id desc limit 1));

--
-- Migrate shipping_type_items
--
INSERT INTO public.shipping_type_items (shipping_type,shipping_type_description,cost) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. shipping_type_item')
as t1(
    shipping_type integer,
    shipping_type_description character varying(50),
    cost real
);

--
-- Migrate shipping_types
--
INSERT INTO public.shipping_types (shipping_type,shipping_type_description) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. shipping_type')
as t1(
    shipping_type integer,
    shipping_type_description character varying(50)
);

--
-- Migrate system_transactions
--
INSERT INTO public.system_transactions (system_transaction_id,transaction_type,user_id,merchant_id,session_id,client_address,system_transaction_date,system_transaction_time,merchant_note_id,change_id,ach_seq_number,order_id,programming_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. system_transaction')
as t1(
    system_transaction_id integer,
    transaction_type character varying(36),
    user_id integer,
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

DELETE FROM public.system_transactions WHERE 
merchant_id = 'BECKER' 
OR merchant_id like '''%' 
OR merchant_id LIKE 'TEST%'
OR merchant_id LIKE '%+%'
OR merchant_id LIKE '%-%'
OR merchant_id LIKE '%FAT%'
OR merchant_id = ''
OR char_length(merchant_id) > 16;

--
-- Convert system_transactions.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE system_transactions ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate tgates
--
INSERT INTO public.tgates (merchant_id,tg_rate,tg_per_item,tg_statement) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tgate')
as t1(
    merchant_id character varying(50),
    tg_rate real,
    tg_per_item real,
    tg_statement real
);


DELETE FROM public.tgates WHERE 
merchant_id = 'BECKER' 
OR merchant_id like '''%' 
OR merchant_id LIKE 'TEST%'
OR merchant_id LIKE '%+%'
OR merchant_id LIKE '%-%'
OR merchant_id LIKE '%FAT%'
OR merchant_id = '';

--
-- Convert tgates.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE tgates ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate timeline_entries
--
INSERT INTO public.timeline_entries (merchant_id,timeline_item,timeline_date_completed,action_flag) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. timeline_entries')
as t1(
    merchant_id character varying(50),
    timeline_item character varying(50),
    timeline_date_completed date,
    action_flag boolean
);


DELETE FROM public.timeline_entries WHERE 
merchant_id = 'BECKER' 
OR merchant_id like '''%' 
OR merchant_id LIKE 'TEST%'
OR merchant_id LIKE '%+%'
OR merchant_id LIKE '%-%'
OR merchant_id LIKE '%FAT%'
OR merchant_id = '';

--
-- Convert timeline_entries.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE timeline_entries ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate timeline_items
--
INSERT INTO public.timeline_items (timeline_item,timeline_item_description) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. timeline_item')
as t1(
    timeline_item character varying(50),
    timeline_item_description character varying(100)
);

--
-- Migrate transaction_types
--
INSERT INTO public.transaction_types (transaction_type,transaction_type_description) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. transaction_type')
as t1(
    transaction_type character varying(36),
    transaction_type_description character varying(50)
);

--
-- Migrate usaepay_rep_gtwy_add_costs
--
INSERT INTO public.usaepay_rep_gtwy_add_costs (old_id,name,cost) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. usaepay_rep_gtwy_add_cost')
as t1(
    id integer,
    name character varying(255),
    cost real
);

-- SELECT setval ('usaepay_rep_gtwy_add_costs_id_seq', (select id FROM axia_legacy. usaepay_rep_gtwy_add_costs order by id desc limit 1));

--
-- Migrate usaepay_rep_gtwy_costs
--
INSERT INTO public.usaepay_rep_gtwy_costs (old_id,name,cost) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. usaepay_rep_gtwy_cost')
as t1(
    id integer,
    name character varying(255),
    cost real
);

-- SELECT setval ('usaepay_rep_gtwy_costs_id_seq', (select id FROM axia_legacy. usaepay_rep_gtwy_costs order by id desc limit 1));

--
-- Migrate user_bet_tables
--
INSERT INTO public.user_bet_tables (bet_code,user_id,network,rate,pi) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. user_bet_table')
as t1(
    bet_code character varying(50),
    user_id integer,
    network character varying(20),
    rate real,
    pi real
);

--
-- Migrate user_permissions
--
-- INSERT INTO public.user_permissions (user_id, permission_id) 
-- select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. user_permission')
-- as t1(
--     user_id integer,
--     permission_id integer
-- );

--
-- Migrate user_types
--
INSERT INTO public.user_types (user_type, user_type_description) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. user_type')
as t1(
    user_type character varying(36),
    user_type_description character varying(50)
);



--
-- Migrate uw_approvalinfo_merchant_xrefs
--
INSERT INTO public.uw_approvalinfo_merchant_xrefs (merchant_id,approvalinfo_id,verified_option_id,notes) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_approvalinfo_merchant_xref')
as t1(
    merchant_id character varying(255),
    approvalinfo_id integer,
    verified_option_id integer,
    notes text
);

--
-- Convert uw_approvalinfo_merchant_xrefs.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE uw_approvalinfo_merchant_xrefs ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate uw_approvalinfos
--
INSERT INTO public.uw_approvalinfos (old_id,name,priority,verified_type) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_approvalinfo')
as t1(
    id integer,
    name character varying(99),
    priority integer,
    verified_type uw_verified_type
);

-- SELECT setval ('uw_approvalinfos_id_seq', (select id FROM axia_legacy. uw_approvalinfos order by id desc limit 1));

--
-- Migrate uw_infodoc_merchant_xrefs
--
INSERT INTO public.uw_infodoc_merchant_xrefs (merchant_id,infodoc_id,received_id,notes) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_infodoc_merchant_xref')
as t1(
    merchant_id character varying(255),
    infodoc_id integer,
    received_id integer,
    notes text
);

--
-- Convert uw_infodoc_merchant_xrefs.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE uw_infodoc_merchant_xrefs ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate uw_infodocs  
--
INSERT INTO public.uw_infodocs (old_id, name, priority, required) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_infodoc')
as t1(
    id integer,
    name character varying(99),
    priority integer,
    required boolean
);

-- SELECT setval ('uw_infodocs_id_seq', (select id FROM axia_legacy. uw_infodocs order by id desc limit 1));

--
-- Migrate uw_receiveds  
--
INSERT INTO public.uw_receiveds (old_id, name) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_received')
as t1(
    id integer,
    name character varying(11)
);

-- SELECT setval ('uw_received_id_seq', (select id FROM axia_legacy. uw_received order by id desc limit 1));

--
-- Migrate uw_status_merchant_xrefs
--
INSERT INTO public.uw_status_merchant_xrefs (merchant_id,status_id,datetime,notes) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_status_merchant_xref')
as t1(
    merchant_id character varying(255),
    status_id integer,
    datetime timestamp without time zone,
    notes text
);

--
-- Convert uw_status_merchant_xrefs.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE uw_status_merchant_xrefs ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate uw_statuses  
--
INSERT INTO public.uw_statuses (old_id,name,priority) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_status')
as t1(
    id integer,
    name character varying(99),
    priority integer
);

-- SELECT setval ('uw_statuses_id_seq', (select id FROM axia_legacy. uw_statuses order by id desc limit 1));

--
-- Migrate uw_verified_options  
--
INSERT INTO public.uw_verified_options (old_id,name,verified_type) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. uw_verified_option')
as t1(    
    id integer,
    name character varying(37),
    verified_type uw_verified_type
);

-- SELECT setval ('uw_verified_options_id_seq', (select id FROM axia_legacy. uw_verified_options order by id desc limit 1));

--
-- Migrate vendors
--
INSERT INTO public.vendors (vendor_id,vendor_description,rank) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. vendor')
as t1(
    vendor_id integer,
    vendor_description character varying(50),
    rank integer
);

--
-- Migrate virtual_check_webs
--
INSERT INTO public.virtual_check_webs (merchant_id,vcweb_mid,vcweb_web_based_rate,vcweb_web_based_pi,vcweb_monthly_fee,vcweb_gateway_fee) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. virtual_check_web')
as t1(
    merchant_id character varying(50),
    vcweb_mid character varying(20),
    vcweb_web_based_rate real,
    vcweb_web_based_pi real,
    vcweb_monthly_fee real,
    vcweb_gateway_fee real
);

--
-- Convert virtual_checks.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE virtual_check_webs ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate virtual_checks
--
INSERT INTO public.virtual_checks (merchant_id,vc_mid,vc_web_based_rate,vc_web_based_pi,vc_monthly_fee,vc_gateway_fee) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. virtual_check')
as t1(
    merchant_id character varying(50),
    vc_mid character varying(20),
    vc_web_based_rate real,
    vc_web_based_pi real,
    vc_monthly_fee real,
    vc_gateway_fee real
);

--
-- Convert virtual_checks.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE virtual_checks ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);

--
-- Migrate warranties
--
INSERT INTO public.warranties (warranty,warranty_description,cost) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. warranty')
as t1(
    warranty integer,
    warranty_description character varying(50),
    cost real
);

--
-- Migrate webpass
--
INSERT INTO public.webpasses (merchant_id,wp_rate,wp_per_item,wp_statement) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. webpass')
as t1(
    merchant_id character varying(50),
    wp_rate real,
    wp_per_item real,
    wp_statement real
);

--
-- Convert webpasses.merchant_id FROM axia_legacy. varchar to integer
--

--ALTER TABLE webpasses ALTER COLUMN merchant_id TYPE bigint USING CAST(merchant_id AS bigint);


INSERT INTO public.tickler_availabilities (id, name) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_availabilities')
as t1(
    id character varying(36),
    name character varying(255)
);

--
-- Name: tickler_availabilities_leads; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_availabilities_leads (id, availability_id, lead_id) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_availabilities_leads')
as t1(
    id character varying(36),
    availability_id character varying(36),
    lead_id character varying(36)
);


--
-- Name: tickler_companies; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_companies (id, name, created,modified) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_companies')
as t1(
    id character varying(36),
    name character varying(100),
    created timestamp without time zone,
    modified timestamp without time zone
);

--
-- Name: tickler_equipments; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_equipments (id, name, created,modified) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_equipments')
as t1(
    id character varying(36),
    name character varying(100),
    created timestamp without time zone,
    modified timestamp without time zone
);

--
-- Name: tickler_followups; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_followups (id, name, created,modified) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_followups')
as t1(
    id character varying(36),
    name character varying(100),
    created timestamp without time zone,
    modified timestamp without time zone
);

--
-- Name: tickler_leads; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_leads (id, business_name,business_address,city,state_id,zip,phone,mobile,fax,decision_maker,decision_maker_title,other_contact,other_contact_title,email,website,company_id,equipment_id,referer_id,reseller_id,user_id,status_id,likes1,likes2,likes3,dislikes1,dislikes2,dislikes3,created,modified,call_date,meeting_date,sign_date,app_created,nlp_id,volume,lat,lng ) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_leads')
as t1(
    id character varying(36),
    business_name character varying(100),
    business_address character varying(100),
    city character varying(60),
    state_id character varying(36),
    zip character varying(12),
    phone character varying(20),
    mobile character varying(20),
    fax character varying(20),
    decision_maker character varying(50),
    decision_maker_title character varying(50),
    other_contact character varying(50),
    other_contact_title character varying(50),
    email character varying(100),
    website character varying(1000),
    company_id character varying(36),
    equipment_id character varying(36),
    referer_id character varying(36),
    reseller_id character varying(36),
    user_id integer,
    status_id character varying(36),
    likes1 character varying(100),
    likes2 character varying(100),
    likes3 character varying(100),
    dislikes1 character varying(100),
    dislikes2 character varying(100),
    dislikes3 character varying(100),
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


--
-- Name: tickler_loggable_logs; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_loggable_logs (id, action,model,foreign_key,source_id,content,created ) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_loggable_logs')
as t1(
    id integer,
    action character varying(150),
    model character varying(150),
    foreign_key character varying(150),
    source_id integer,
    content text,
    created timestamp without time zone
);


--
-- Name: tickler_referers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_referers (id,name,created,modified ) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_referers')
as t1(
    id character varying(36),
    name character varying(100),
    created timestamp without time zone,
    modified timestamp without time zone
);

--
-- Name: tickler_resellers; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_resellers (id,name,created,modified ) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_resellers')
as t1(
    id character varying(36),
    name character varying(100),
    created timestamp without time zone,
    modified timestamp without time zone
);

--
-- Name: tickler_schema_migrations; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_schema_migrations (id,class,type,created ) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_schema_migrations')
as t1(
    id integer,
    class character varying(255),
    type character varying(50),
    created timestamp without time zone
);


--
-- Name: tickler_states; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_states (id, name,state) 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_states')
as t1(
    id character varying(36),
    name character varying(2),
    state character varying(30)
);

--
-- Name: tickler_statuses; Type: TABLE; Schema: public; Owner: axia; Tablespace: 
--

INSERT INTO public.tickler_statuses (id, name, created, modified, "order") 
select * FROM axia_legacy. dblink('pg84', 'select * FROM axia_legacy. tickler_statuses')
as t1(
    id character varying(36),
    name character varying(100),
    created timestamp without time zone,
    modified timestamp without time zone,
    "order" integer
);


-------------------------------------------------------------------------------------------------------------
-------------------------------DATA ENCODING SANITATION FUNCTIONS
-------------------------------------------------------------------------------------------------------------

-- CREATE OR REPLACE FUNCTION findAllOCTL_LATIN1Chars()
-- RETURNS TEXT
-- AS $$
-- DECLARE
--     queriesArr text[] := array(SELECT 'SELECT '||column_name||'::bytea FROM axia_legacy. '||table_name||' WHERE '||column_name|| ' SIMILAR TO ' || E'\'%[^\\x20-\\x7e]+%\';' FROM axia_legacy. information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');    
--     i int;
--     x text := '';         
--     result TEXT := '';
--     code TEXT:='';
--     finalResult text[];
-- BEGIN    
--     FOR i IN array_lower(queriesArr,1) .. array_upper(queriesArr,1)
--         LOOP        
--            BEGIN
--               FOR x IN EXECUTE(queriesArr[i]) LOOP                    
--                     FOR code in SELECT regexp_matches(x,'\\([0-9]{1,3})', 'g') LOOP
--                         IF result > '' THEN
--                             result := result || ',' || trim(leading '{' FROM axia_legacy. trim(trailing '}'FROM axia_legacy. code));
--                         ELSE
--                             result := trim(leading '{' FROM axia_legacy. trim(trailing '}'FROM axia_legacy. code));
--                         END IF;
--                     END LOOP;                                
--               END LOOP;
--              exception when others then 
--                    RAISE NOTICE 'Invalid input syntax for bytea for query: %', queriesArr[i];
--           END;--exception
--     END LOOP;
-- finalResult := ARRAY(
--   select unnest(string_to_array(result, ',')) as e
--    union
--   select unnest(string_to_array(result, ',')) as e
--    order by e);
-- RETURN finalResult;
-- END;
-- $$ LANGUAGE plpgsql;
-- -------------------------------------------------------------------------------------------------------
-- CREATE OR REPLACE FUNCTION replaceOCTALCHAR()
-- RETURNS text
-- AS $$
-- DECLARE
--     queriesArr text[] := array(SELECT 'SELECT id, '||column_name||'::bytea FROM axia_legacy. '||table_name||' WHERE '||column_name|| ' SIMILAR TO ' || E'\'%[^\\x20-\\x7e]+%\';' FROM axia_legacy. information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');    
--     queryIndex int;
--     dataSet text := '';         
--     idCol text := ''; 
--     result Text;
--     code TEXT:='';
--     finalResult text;
--     cntReplaced bigint:= 0;
--     cntRecords bigint:= 0;
--     rowsCnt int := 0;
--     octalVal INTEGER; --Doesn't have to be in the octal numerical system.
--     runOnce BOOLEAN := false;
--     updateSuccess BOOLEAN;
-- BEGIN
--     RAISE NOTICE 'Searching using % queries...', array_upper(queriesArr,1);
--     FOR queryIndex IN array_lower(queriesArr,1) .. array_upper(queriesArr,1)
--         LOOP        
--            BEGIN              
--               RAISE NOTICE ' >> Executed Query: %', queriesArr[queryIndex];
--               FOR idCol, dataSet IN EXECUTE(queriesArr[queryIndex]) LOOP                  
--                 rowsCnt := rowsCnt + 1;
--                     FOR code in SELECT regexp_matches(dataSet,'\\([0-9]{1,3})', 'g') LOOP
--                         octalVal := trim(leading '{' FROM axia_legacy. trim(trailing '}'FROM axia_legacy. code)) ::INTEGER;
--                         result := getValidChar(octalVal);
--                         IF result != 'skip' THEN
--                             If runOnce = false then
--                                 --finalResult := replace( dataSet::bytea::text, E'\\' || octalVal, result);
--                                 finalResult := dataSet::bytea::text;
--                                 runOnce := true;
--                             END IF;
--                             finalResult := replace( finalResult, E'\\' || octalVal, result);
--                             cntReplaced := cntReplaced + 1;                            
--                         END IF;                      
--                     END LOOP;  -- octate codes loop                  
--                -- RAISE NOTICE '>> Row % data: %', rowsCnt, dataSet;
--                -- RAISE NOTICE '>> Modified data: %', finalResult; --select replace( '~', E'\\'|| '176', chr(126))::bytea;                 
--                 runOnce := false;
--                 cntRecords := cntRecords + 1;
--                 updateSuccess := updateCorruptedRecordsEncoding(queryIndex, finalResult, idCol);
--                 if updateSuccess = false then
--                     RETURN 'FATAL ERROR!: Update failed for table containing UUID: '||idCol||'. Related query: ' || queriesArr[queryIndex];
--                 ELSE
--                     RAISE NOTICE '>> Updated data: %', finalResult;
--                 end if;
--               END LOOP; --dataSet Loop
--              exception when others then 
--                    RAISE NOTICE 'Invalid input syntax for bytea for query: %', queriesArr[queryIndex];
--           END;--exception
--      rowsCnt := 0; -- reset count of number of rows returned per query
--     END LOOP;
-- RETURN cntReplaced || ' octet character replacements made in '|| cntRecords ||' records.';
-- END;
-- $$ LANGUAGE plpgsql;
-- 
-- -- select overlay(
-- -- (EXCECUTE "SELECT mailing_phone::bytea FROM axia_legacy. onlineapp_applications WHERE mailing_phone SIMILAR TO '%[^\x20-\x7e]+%';" placing 'hom' FROM axia_legacy. 2 for 4
-- -- );
-- 
-- CREATE OR REPLACE FUNCTION getValidChar(indexNum int)
-- returns text
-- AS $$
-- declare
--     finalResult integer[];    
-- begin
-- -- The following values reprecent a value that can be passed to the chr(##) internal function which will return a character in the encoding that matches
-- -- current database encoding.
--     finalResult[11] := 9;    finalResult[020] := 32;     finalResult[026] := 32;     finalResult[027] := 32;     finalResult[030] := 32;
--     finalResult[031] := 32;  finalResult[177] := 32;     finalResult[200] := 32;     finalResult[205] := 32;     finalResult[211] := 32;
--     finalResult[212] := 32;  finalResult[215] := 45;     finalResult[216] := 32;     finalResult[222] := 39;     finalResult[223] := 32;
--     finalResult[224] := 32;  finalResult[226] := 32;     finalResult[230] := 32;     finalResult[231] := 32;     finalResult[232] := 32;
--     finalResult[235] := 32;  finalResult[240] := 32;     finalResult[242] := 32;     finalResult[251] := 101;    finalResult[253] := 32;
--     finalResult[255] := 105; finalResult[277] := 101;    finalResult[326] := 32;     finalResult[342] := 39;     finalResult[351] := 101;
--     finalResult[355] := 105;
-- 
--   
-- -- The following values are all set to zero so that they can be specifically identified later. 
-- -- These values are not expected to be replaced by chr(##)
-- -- 012 015 201 202 204 241 244 260 261 265 270 271 301 302 314 321 324 341 343 347
--     finalResult[12] := 0; finalResult[270] := 0;
--     finalResult[15] := 0; finalResult[271] := 0;
--     finalResult[201] := 0; finalResult[301] := 0;
--     finalResult[202] := 0; finalResult[302] := 0;
--     finalResult[204] := 0; finalResult[314] := 0;
--     finalResult[241] := 0; finalResult[321] := 0;
--     finalResult[244] := 0; finalResult[324] := 0;
--     finalResult[260] := 0; finalResult[341] := 0;
--     finalResult[261] := 0; finalResult[343] := 0;
--     finalResult[265] := 0; finalResult[347] := 0;
--     finalResult[274] := 0;
-- 
-- -- Handle special cases first
-- if indexNum = 274 then
--     return '1/4';
-- end if;
-- if finalResult[indexNum] = 0 then
--     return 'skip';
-- END if;
-- -- Handle all other cases
-- if finalResult[indexNum] is null then
--     return '';
-- ELSE
--     return chr(finalResult[indexNum]);
-- END if;
-- END;
-- $$ LANGUAGE plpgsql;
-- 
-- 
-- CREATE OR REPLACE FUNCTION updateCorruptedRecordsEncoding(queryIndx int, sanitizedDataSet text, uuidID varchar(36))
-- RETURNS BOOLEAN
-- AS $$
-- DECLARE
--     updatesQryArr text[] := array(SELECT 'UPDATE '||table_name||' SET '||column_name||'='||quote_literal(sanitizedDataSet)|| ' where ID=' ||quote_literal(uuidID) ||';' FROM axia_legacy. information_schema.columns where table_catalog = 'axia' and (data_type = 'character varying' or data_type = 'text') and table_schema = 'public' and column_name != 'id' and column_name not like '%\_id');        
-- BEGIN
--     BEGIN
--     EXECUTE updatesQryArr[queryIndx];
--     exception when others then 
--         RAISE NOTICE 'Update failed for table containing UUID: %. Related query: %', uuidID, updatesQryArr[queryIndx]; --if error ocurs
--     END;--exception
-- RETURN true;
-- END;
-- $$ LANGUAGE plpgsql;
-- -------------------------------------------------------------------------------------------------------------
-- -------------------------------------------------------------------------------------------------------------
-- 
-- CREATE OR REPLACE FUNCTION sanitizeData(corruptData text)
-- RETURNS text
-- AS $$
-- DECLARE
--     encodingName text := 'WIN1250';
--     selectsQryArr text:= 'SELECT merchant_id, convert_from(merchant_dba::bytea, '|| quote_literal(encodingName) ||') FROM axia_legacy. merchant WHERE merchant_dba SIMILAR TO '|| E'\'%[^\\x20-\\x7e]+%\';';
--     
--     resultData text;
--     cleanData text;
--     idCol text;
--     strIndex int;
-- BEGIN
-- 	--EXECUTE updatesQryArr into resultData;
--         FOR idCol, resultData IN EXECUTE(selectsQryArr) LOOP
--                 --EXECUTE 'UPDATE merchants SET merchant_dba=' || quote_literal(resultData) || 'WHERE id=' || quote_literal(idCol);
--               RAISE NOTICE '% %', idCol, resultData;                  
--         END LOOP;  -- octate codes loop
--             --RAISE NOTICE '%',  substr(resultData, strIndex,1)::bytea;--if error ocurs
--             --RAISE NOTICE '%',  substr(resultData, 7,1)::bytea;	
-- return cleanData;
-- END;
-- $$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION findEmptyTables()
RETURNS VOID AS $$
DECLARE
queriesArr text[] := array(SELECT 'SELECT count(id) FROM axia_legacy. '||table_name||';' FROM axia_legacy. information_schema.tables where table_catalog = 'axia' and table_schema = 'public');
cnt integer:= 1;
recCount integer;
BEGIN
FOR cnt IN array_lower(queriesArr,1) .. array_upper(queriesArr,1) LOOP    
    FOR recCount IN EXECUTE(queriesArr[cnt]) LOOP    
        if recCount = 0 THEN 
            RAISE NOTICE 'EMPTY TABLE FOUND: %', right(queriesArr[cnt], length(queriesArr[cnt]) - strpos(queriesArr[cnt], 'FROM') - 4); 
        END IF;
    END LOOP;
END LOOP;
END; 
$$ LANGUAGE plpgsql;	

CREATE OR REPLACE FUNCTION drop_not_null_on_old() RETURNS void AS $$
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
		RAISE NOTICE 'Updating: %', left(right(rec.name, length(rec.name) - strpos(rec.name, 'TABLE') - 4), strpos(right(rec.name, length(rec.name) - strpos(rec.name, 'TABLE') - 4), ' ALTER'));		
        cmd := cmd || rec.name;		
    END LOOP;
    
    EXECUTE cmd;
    RETURN;
END;
$$ LANGUAGE plpgsql;

select drop_not_null_on_old();