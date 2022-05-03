

DROP TABLE "public"."achs";
DROP TABLE "public"."acos";
DROP TABLE "public"."address_types";
DROP TABLE "public"."addresses";
DROP TABLE "public"."adjustments";
DROP TABLE "public"."admin_entity_views";
DROP TABLE "public"."amexes";
DROP TABLE "public"."aros";
DROP TABLE "public"."aros_acos";
DROP TABLE "public"."authorizes";
DROP TABLE "public"."bankcards";
DROP TABLE "public"."bets";
DROP TABLE "public"."cancellation_fees";
DROP TABLE "public"."card_types";
DROP TABLE "public"."change_types";
DROP TABLE "public"."check_guarantees";
DROP TABLE "public"."commission_pricings";
DROP TABLE "public"."commission_report_olds";
DROP TABLE "public"."commission_reports";
DROP TABLE "public"."compliant_merchants_mvs";
DROP TABLE "public"."debit_acquirers";
DROP TABLE "public"."debits";
DROP TABLE "public"."discover_bets";
DROP TABLE "public"."discover_user_bet_tables";
DROP TABLE "public"."discovers";
DROP TABLE "public"."ebts";
DROP TABLE "public"."entities";
DROP TABLE "public"."equipment_items";
DROP TABLE "public"."equipment_programming_type_xrefs";
DROP TABLE "public"."equipment_programmings";
DROP TABLE "public"."equipment_types";
DROP TABLE "public"."gateway0";
DROP TABLE "public"."gateway1";
DROP TABLE "public"."gateway2";
DROP TABLE "public"."gateways";
DROP TABLE "public"."gift_cards";
DROP TABLE "public"."groups";
DROP TABLE "public"."last_deposit_reports";
DROP TABLE "public"."merchant_ach_app_statuses";
DROP TABLE "public"."merchant_ach_billing_options";
DROP TABLE "public"."merchant_achs";
DROP TABLE "public"."merchant_acquirers";
DROP TABLE "public"."merchant_banks";
DROP TABLE "public"."merchant_bins";
DROP TABLE "public"."merchant_cancellation_subreasons";
DROP TABLE "public"."merchant_cancellations";
DROP TABLE "public"."merchant_card_types";
DROP TABLE "public"."merchant_changes";
DROP TABLE "public"."merchant_notes";
DROP TABLE "public"."merchant_owners";
DROP TABLE "public"."merchant_pcis";
DROP TABLE "public"."merchant_reference_types";
DROP TABLE "public"."merchant_references";
DROP TABLE "public"."merchant_reject_lines";
DROP TABLE "public"."merchant_reject_recurrances";
DROP TABLE "public"."merchant_reject_statuses";
DROP TABLE "public"."merchant_reject_types";
DROP TABLE "public"."merchant_rejects";
DROP TABLE "public"."merchant_uw_final_approveds";
DROP TABLE "public"."merchant_uw_final_statuses";
DROP TABLE "public"."merchant_uws";
DROP TABLE "public"."merchants";
DROP TABLE "public"."networks";
DROP TABLE "public"."non_compliant_merchants_cs_mv";
DROP TABLE "public"."non_compliant_merchants_mv";
DROP TABLE "public"."note_types";
DROP TABLE "public"."onlineapp_acos";
DROP TABLE "public"."onlineapp_apips";
DROP TABLE "public"."onlineapp_applications";
DROP TABLE "public"."onlineapp_aros";
DROP TABLE "public"."onlineapp_aros_acos";
DROP TABLE "public"."onlineapp_email_timeline_subjects";
DROP TABLE "public"."onlineapp_email_timelines";
DROP TABLE "public"."onlineapp_epayments";
DROP TABLE "public"."onlineapp_groups";
DROP TABLE "public"."onlineapp_settings";
DROP TABLE "public"."onlineapp_users";
DROP TABLE "public"."orderitem_types";
DROP TABLE "public"."orderitems";
DROP TABLE "public"."orderitems_replacements";
DROP TABLE "public"."orders";
DROP TABLE "public"."partners";
DROP TABLE "public"."pci_billing_histories";
DROP TABLE "public"."pci_billing_types";
DROP TABLE "public"."pci_billings";
DROP TABLE "public"."pci_compliance_date_types";
DROP TABLE "public"."pci_compliance_status_logs";
DROP TABLE "public"."pci_compliances";
DROP TABLE "public"."pci_reminders_mv";
DROP TABLE "public"."permission_groups";
DROP TABLE "public"."pricing_matrixes";
DROP TABLE "public"."products_and_services";
DROP TABLE "public"."products_services_types";
DROP TABLE "public"."referer_products_services_xrefs";
DROP TABLE "public"."referers";
DROP TABLE "public"."referers_bets";
DROP TABLE "public"."rep_cost_structures";
DROP TABLE "public"."rep_partner_xrefs";
DROP TABLE "public"."residual_pricings";
DROP TABLE "public"."residual_reports";
DROP TABLE "public"."sales_goal_archives";
DROP TABLE "public"."sales_goals";
DROP TABLE "public"."saq_answers";
DROP TABLE "public"."saq_control_scan_unboardeds";
DROP TABLE "public"."saq_control_scans";
DROP TABLE "public"."saq_eligible_mv";
DROP TABLE "public"."saq_merchant_pci_email_sents";
DROP TABLE "public"."saq_merchant_pci_emails";
DROP TABLE "public"."saq_merchant_survey_xrefs";
DROP TABLE "public"."saq_merchants";
DROP TABLE "public"."saq_prequalifications";
DROP TABLE "public"."saq_questions";
DROP TABLE "public"."saq_survey_question_xrefs";
DROP TABLE "public"."saq_surveys";
DROP TABLE "public"."schema_migrations";
DROP TABLE "public"."shipping_type_items";
DROP TABLE "public"."shipping_types";
DROP TABLE "public"."system_transactions";
DROP TABLE "public"."tgates";
DROP TABLE "public"."timeline_entries";
DROP TABLE "public"."timeline_items";
DROP TABLE "public"."transaction_types";
DROP TABLE "public"."usaepay_rep_gtwy_add_costs";
DROP TABLE "public"."usaepay_rep_gtwy_costs";
DROP TABLE "public"."user_bet_tables";
DROP TABLE "public"."user_permissions";
DROP TABLE "public"."user_types";
DROP TABLE "public"."users";
DROP TABLE "public"."uw_approvalinfo_merchant_xrefs";
DROP TABLE "public"."uw_approvalinfos";
DROP TABLE "public"."uw_infodoc_merchant_xrefs";
DROP TABLE "public"."uw_infodocs";
DROP TABLE "public"."uw_receiveds";
DROP TABLE "public"."uw_status_merchant_xrefs";
DROP TABLE "public"."uw_statuses";
DROP TABLE "public"."uw_verified_options";
DROP TABLE "public"."vanilla_acos";
DROP TABLE "public"."vanilla_aros";
DROP TABLE "public"."vanilla_aros_acos";
DROP TABLE "public"."vanilla_posts";
DROP TABLE "public"."vanilla_roles";
DROP TABLE "public"."vanilla_users";
DROP TABLE "public"."vanilla_widgets";
DROP TABLE "public"."vendors";
DROP TABLE "public"."virtual_check_webs";
DROP TABLE "public"."virtual_checks";
DROP TABLE "public"."warranties";
DROP TABLE "public"."webpasses";


CREATE TABLE "public"."achs" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"ach_mid" integer DEFAULT NULL,
	"ach_expected_annual_sales" float DEFAULT NULL,
	"ach_average_transaction" float DEFAULT NULL,
	"ach_estimated_max_transaction" float DEFAULT NULL,
	"ach_written_pre_auth" float DEFAULT NULL,
	"ach_nonwritten_pre_auth" float DEFAULT NULL,
	"ach_merchant_initiated_perc" float DEFAULT NULL,
	"ach_consumer_initiated_perc" float DEFAULT NULL,
	"ach_monthly_gateway_fee" float DEFAULT NULL,
	"ach_monthly_minimum_fee" float DEFAULT NULL,
	"ach_statement_fee" float DEFAULT NULL,
	"ach_batch_upload_fee" float DEFAULT NULL,
	"ach_reject_fee" float DEFAULT NULL,
	"ach_add_bank_orig_ident_fee" float DEFAULT NULL,
	"ach_file_fee" float DEFAULT NULL,
	"ach_eft_ccd_nw" float DEFAULT NULL,
	"ach_eft_ccd_w" float DEFAULT NULL,
	"ach_eft_ppd_nw" float DEFAULT NULL,
	"ach_eft_ppd_w" float DEFAULT NULL,
	"ach_application_fee" float DEFAULT NULL,
	"ach_expedite_fee" float DEFAULT NULL,
	"ach_tele_training_fee" float DEFAULT NULL,
	"ach_mi_w_dsb_bank_name" varchar(50) DEFAULT NULL,
	"ach_mi_w_dsb_routing_number" integer DEFAULT NULL,
	"ach_mi_w_dsb_account_number" integer DEFAULT NULL,
	"ach_mi_w_fee_bank_name" varchar(50) DEFAULT NULL,
	"ach_mi_w_fee_routing_number" integer DEFAULT NULL,
	"ach_mi_w_fee_account_number" integer DEFAULT NULL,
	"ach_mi_w_rej_bank_name" varchar(50) DEFAULT NULL,
	"ach_mi_w_rej_routing_number" integer DEFAULT NULL,
	"ach_mi_w_rej_account_number" integer DEFAULT NULL,
	"ach_mi_nw_dsb_account_number" integer DEFAULT NULL,
	"ach_mi_nw_dsb_routing_number" integer DEFAULT NULL,
	"ach_mi_nw_dsb_bank_name" varchar(50) DEFAULT NULL,
	"ach_mi_nw_fee_bank_name" varchar(50) DEFAULT NULL,
	"ach_mi_nw_fee_routing_number" integer DEFAULT NULL,
	"ach_mi_nw_fee_account_number" integer DEFAULT NULL,
	"ach_mi_nw_rej_bank_name" varchar(50) DEFAULT NULL,
	"ach_mi_nw_rej_routing_number" integer DEFAULT NULL,
	"ach_mi_nw_rej_account_number" integer DEFAULT NULL,
	"ach_ci_w_dsb_bank_name" varchar(50) DEFAULT NULL,
	"ach_ci_w_dsb_routing_number" integer DEFAULT NULL,
	"ach_ci_w_dsb_account_number" integer DEFAULT NULL,
	"ach_ci_w_fee_bank_name" varchar(50) DEFAULT NULL,
	"ach_ci_w_fee_routing_number" integer DEFAULT NULL,
	"ach_ci_w_fee_account_number" integer DEFAULT NULL,
	"ach_ci_w_rej_bank_name" varchar(50) DEFAULT NULL,
	"ach_ci_w_rej_routing_number" integer DEFAULT NULL,
	"ach_ci_w_rej_account_number" integer DEFAULT NULL,
	"ach_ci_nw_dsb_bank_name" varchar(50) DEFAULT NULL,
	"ach_ci_nw_dsb_routing_number" integer DEFAULT NULL,
	"ach_ci_nw_dsb_account_number" integer DEFAULT NULL,
	"ach_ci_nw_fee_bank_name" varchar(50) DEFAULT NULL,
	"ach_ci_nw_fee_routing_number" integer DEFAULT NULL,
	"ach_ci_nw_fee_account_number" integer DEFAULT NULL,
	"ach_ci_nw_rej_bank_name" varchar(50) DEFAULT NULL,
	"ach_ci_nw_rej_routing_number" integer DEFAULT NULL,
	"ach_ci_nw_rej_account_number" integer DEFAULT NULL,
	"ach_rate" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX ach_merchantid ON "public"."achs"("merchant_id");
CREATE INDEX ach_mid ON "public"."achs"("ach_mid");
CREATE INDEX achs_merchant_id_idx ON "public"."achs"("merchant_id");

CREATE TABLE "public"."acos" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT NULL,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT NULL,
	"alias" varchar(255) DEFAULT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."address_types" (
	"id" serial NOT NULL,
	"address_type" varchar(10) NOT NULL,
	"address_type_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."addresses" (
	"id" serial NOT NULL,
	"address_id" integer NOT NULL,
	"merchant_id" integer NOT NULL,
	"address_type" varchar(10) NOT NULL,
	"merchant_owner_id" integer DEFAULT NULL,
	"address_title" varchar(50) DEFAULT NULL,
	"address_street" varchar(100) DEFAULT NULL,
	"address_city" varchar(50) DEFAULT NULL,
	"address_state" varchar(2) DEFAULT NULL,
	"address_zip" varchar(20) DEFAULT NULL,
	"address_phone" varchar(20) DEFAULT NULL,
	"address_fax" varchar(20) DEFAULT NULL,
	"address_phone2" varchar(20) DEFAULT NULL,
	"address_phone_ext" varchar(5) DEFAULT NULL,
	"address_phone2_ext" varchar(5) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX address_owner_uidx ON "public"."addresses"("merchant_owner_id");
CREATE INDEX address_merchant_type_idx ON "public"."addresses"("address_type", "merchant_id");
CREATE INDEX addresses_merchant_id_idx ON "public"."addresses"("merchant_id");

CREATE TABLE "public"."adjustments" (
	"id" serial NOT NULL,
	"adj_seq_number" integer NOT NULL,
	"user_id" integer NOT NULL,
	"adj_date" date DEFAULT NULL,
	"adj_description" varchar(100) DEFAULT NULL,
	"adj_amount" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX adjustments_user_idx ON "public"."adjustments"("user_id");

CREATE TABLE "public"."admin_entity_views" (
	"id" serial NOT NULL,
	"user_id" integer NOT NULL,
	"entity" varchar(10) NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX admin_entity_view_user_idx ON "public"."admin_entity_views"("user_id");

CREATE TABLE "public"."amexes" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"amex_processing_rate" float DEFAULT NULL,
	"amex_per_item_fee" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX amexes_merchant_id_idx ON "public"."amexes"("merchant_id");

CREATE TABLE "public"."aros" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT NULL,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT NULL,
	"alias" varchar(255) DEFAULT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."aros_acos" (
	"id" serial NOT NULL,
	"aro_id" integer NOT NULL,
	"aco_id" integer NOT NULL,
	"_create" varchar(2) DEFAULT '0' NOT NULL,
	"_read" varchar(2) DEFAULT '0' NOT NULL,
	"_update" varchar(2) DEFAULT '0' NOT NULL,
	"_delete" varchar(2) DEFAULT '0' NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX aro_aco_key ON "public"."aros_acos"("aro_id", "aco_id");

CREATE TABLE "public"."authorizes" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"mid" varchar(20) DEFAULT NULL,
	"transaction_fee" float DEFAULT NULL,
	"monthly_fee" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX authorizes_merchant_id_idx ON "public"."authorizes"("merchant_id");

CREATE TABLE "public"."bankcards" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"bc_mid" varchar(20) DEFAULT NULL,
	"bet_code" varchar(15) DEFAULT NULL,
	"bc_processing_rate" float DEFAULT NULL,
	"bc_per_item_fee" float DEFAULT NULL,
	"bc_monthly_volume" float DEFAULT NULL,
	"bc_average_ticket" float DEFAULT NULL,
	"bc_max_transaction_amount" float DEFAULT NULL,
	"bc_card_present_swipe" float DEFAULT NULL,
	"bc_card_not_present" float DEFAULT NULL,
	"bc_card_present_imprint" float DEFAULT NULL,
	"bc_direct_to_consumer" float DEFAULT NULL,
	"bc_business_to_business" float DEFAULT NULL,
	"bc_government" float DEFAULT NULL,
	"bc_annual_fee" float DEFAULT NULL,
	"bc_statement_fee" float DEFAULT NULL,
	"bc_min_month_process_fee" float DEFAULT NULL,
	"bc_chargeback_fee" float DEFAULT NULL,
	"bc_aru_fee" float DEFAULT NULL,
	"bc_voice_auth_fee" float DEFAULT NULL,
	"bc_te_amex_number" varchar(255) DEFAULT NULL,
	"bc_te_amex_auth_fee" float DEFAULT NULL,
	"bc_te_diners_club_number" varchar(255) DEFAULT NULL,
	"bc_te_diners_club_auth_fee" float DEFAULT NULL,
	"bc_te_discover_number" varchar(255) DEFAULT NULL,
	"bc_te_discover_auth_fee" float DEFAULT NULL,
	"bc_te_jcb_number" varchar(255) DEFAULT NULL,
	"bc_te_jcb_auth_fee" float DEFAULT NULL,
	"bc_pt_monthly_support_fee" float DEFAULT NULL,
	"bc_pt_online_mer_report_fee" float DEFAULT NULL,
	"bc_pt_mobile_access_fee" float DEFAULT NULL,
	"bc_pt_mobile_transaction_fee" float DEFAULT NULL,
	"bc_pt_application_fee" float DEFAULT NULL,
	"bc_pt_expedite_fee" float DEFAULT NULL,
	"bc_pt_mobile_setup_fee" float DEFAULT NULL,
	"bc_pt_equip_fee" float DEFAULT NULL,
	"bc_pt_phys_prod_tele_train_fee" float DEFAULT NULL,
	"bc_pt_equip_reprog_fee" float DEFAULT NULL,
	"bc_vt_monthly_support_fee" float DEFAULT NULL,
	"bc_vt_gateway_access_fee" float DEFAULT NULL,
	"bc_vt_application_fee" float DEFAULT NULL,
	"bc_vt_expedite_fee" float DEFAULT NULL,
	"bc_vt_prod_tele_train_fee" float DEFAULT NULL,
	"bc_vt_lease_rental_deposit" float DEFAULT NULL,
	"bc_hidden_per_item_fee" float DEFAULT NULL,
	"bc_te_amex_number_disp" varchar(4) DEFAULT NULL,
	"bc_te_diners_club_number_disp" varchar(4) DEFAULT NULL,
	"bc_te_discover_number_disp" varchar(4) DEFAULT NULL,
	"bc_te_jcb_number_disp" varchar(4) DEFAULT NULL,
	"bc_hidden_per_item_fee_cross" float DEFAULT NULL,
	"bc_eft_secure_flag" integer DEFAULT NULL,
	"bc_pos_partner_flag" integer DEFAULT NULL,
	"bc_pt_online_rep_report_fee" float DEFAULT NULL,
	"bc_micros_ip_flag" integer DEFAULT NULL,
	"bc_micros_dialup_flag" integer DEFAULT NULL,
	"bc_micros_per_item_fee" float DEFAULT NULL,
	"bc_te_num_items" float DEFAULT NULL,
	"bc_wireless_flag" integer DEFAULT NULL,
	"bc_wireless_terminals" integer DEFAULT NULL,
	"bc_usaepay_flag" integer DEFAULT NULL,
	"bc_epay_retail_flag" integer DEFAULT NULL,
	"bc_usaepay_rep_gtwy_cost_id" integer DEFAULT NULL,
	"bc_usaepay_rep_gtwy_add_cost_id" integer DEFAULT NULL,
	"bc_card_not_present_internet" float DEFAULT NULL,
	"bc_tgate_flag" integer DEFAULT NULL,
	"bc_petro_flag" integer DEFAULT NULL,
	"bc_risk_assessment" float DEFAULT NULL,
	"bc_gw_gateway_id" integer DEFAULT NULL,
	"bc_gw_rep_rate" float DEFAULT NULL,
	"bc_gw_rep_per_item" float DEFAULT NULL,
	"bc_gw_rep_statement" float DEFAULT NULL,
	"bc_gw_rep_features" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX bankcard_bcmid ON "public"."bankcards"("bc_mid");
CREATE INDEX bankcard_bet ON "public"."bankcards"("bet_code");
CREATE INDEX bankcard_urgac_index ON "public"."bankcards"("bc_usaepay_rep_gtwy_add_cost_id");
CREATE INDEX bankcard_urgc_index ON "public"."bankcards"("bc_usaepay_rep_gtwy_cost_id");
CREATE INDEX bankcards_merchant_id_idx ON "public"."bankcards"("merchant_id");

CREATE TABLE "public"."bets" (
	"id" serial NOT NULL,
	"bet_code" varchar(15) NOT NULL,
	"bet_processing_rate" float DEFAULT NULL,
	"bet_processing_rate_2" float DEFAULT NULL,
	"bet_processing_rate_3" float DEFAULT NULL,
	"bet_per_item_fee" float DEFAULT NULL,
	"bet_per_item_fee_2" float DEFAULT NULL,
	"bet_per_item_fee_3" float DEFAULT NULL,
	"bet_business_rate" float DEFAULT NULL,
	"bet_business_rate_2" float DEFAULT NULL,
	"bet_per_item_fee_bus" float DEFAULT NULL,
	"bet_per_item_fee_bus_2" float DEFAULT NULL,
	"bet_extra_pct" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."cancellation_fees" (
	"id" serial NOT NULL,
	"cancellation_fee_id" integer NOT NULL,
	"cancellation_fee_description" varchar(50) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."card_types" (
	"id" serial NOT NULL,
	"card_type" varchar(10) NOT NULL,
	"card_type_description" varchar(20) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."change_types" (
	"id" serial NOT NULL,
	"change_type" integer NOT NULL,
	"change_type_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."check_guarantees" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"cg_mid" varchar(20) DEFAULT NULL,
	"cg_station_number" varchar(20) DEFAULT NULL,
	"cg_account_number" varchar(20) DEFAULT NULL,
	"cg_transaction_rate" float DEFAULT NULL,
	"cg_per_item_fee" float DEFAULT NULL,
	"cg_monthly_fee" float DEFAULT NULL,
	"cg_monthly_minimum_fee" float DEFAULT NULL,
	"cg_application_fee" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX check_guarantees_merchant_id_idx ON "public"."check_guarantees"("merchant_id");

CREATE TABLE "public"."commission_pricings" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"user_id" integer NOT NULL,
	"c_month" float NOT NULL,
	"c_year" float NOT NULL,
	"multiple" float DEFAULT NULL,
	"r_rate_pct" float DEFAULT NULL,
	"r_per_item_fee" float DEFAULT NULL,
	"r_statement_fee" float DEFAULT NULL,
	"m_rate_pct" float DEFAULT NULL,
	"m_per_item_fee" float DEFAULT NULL,
	"m_statement_fee" float DEFAULT NULL,
	"m_avg_ticket" float DEFAULT NULL,
	"m_monthly_volume" float DEFAULT NULL,
	"m_bet_code" varchar(35) DEFAULT NULL,
	"ref_seq_number" integer DEFAULT NULL,
	"bet_extra_pct" float DEFAULT NULL,
	"res_seq_number" integer DEFAULT NULL,
	"products_services_type" varchar(10) DEFAULT 'BC' NOT NULL,
	"num_items" float DEFAULT NULL,
	"ref_p_value" float DEFAULT NULL,
	"res_p_value" float DEFAULT NULL,
	"r_risk_assessment" float DEFAULT NULL,
	"ref_p_type" text DEFAULT NULL,
	"res_p_type" text DEFAULT NULL,
	"ref_p_pct" integer DEFAULT NULL,
	"res_p_pct" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX commission_pricing_index1 ON "public"."commission_pricings"("merchant_id");
CREATE INDEX commission_pricing_index2 ON "public"."commission_pricings"("user_id");
CREATE INDEX commission_pricing_index3 ON "public"."commission_pricings"("c_month");
CREATE INDEX commission_pricing_index4 ON "public"."commission_pricings"("c_year");
CREATE INDEX commission_pricing_products_services_type ON "public"."commission_pricings"("products_services_type");
CREATE INDEX commission_pricings_merchant_id_idx ON "public"."commission_pricings"("merchant_id");

CREATE TABLE "public"."commission_report_olds" (
	"id" serial NOT NULL,
	"merchant_id_old" varchar(50) NOT NULL,
	"c_month" integer DEFAULT NULL,
	"c_year" integer DEFAULT NULL,
	"c_retail" float DEFAULT NULL,
	"c_rep_cost" float DEFAULT NULL,
	"c_shipping" float DEFAULT NULL,
	"c_app_rtl" float DEFAULT NULL,
	"c_app_cost" float DEFAULT NULL,
	"c_install" float DEFAULT NULL,
	"c_expecting" float DEFAULT NULL,
	"c_from_nw1" float DEFAULT NULL,
	"c_other_cost" float DEFAULT NULL,
	"c_from_other" float DEFAULT NULL,
	"c_business" float DEFAULT NULL,
	"user_id" integer DEFAULT NULL,
	"status" varchar(5) DEFAULT NULL,
	"order_id" integer DEFAULT NULL,
	"axia_invoice_number" integer DEFAULT NULL,
	"ach_seq_number" integer DEFAULT NULL,
	"description" varchar(200) DEFAULT NULL,
	"merchant_id" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX commission_report_olds_merchant_id_idx ON "public"."commission_report_olds"("merchant_id");
CREATE INDEX cr_mid_idx ON "public"."commission_report_olds"("merchant_id");

CREATE TABLE "public"."commission_reports" (
	"id" serial NOT NULL,
	"c_month" integer DEFAULT NULL,
	"c_year" integer DEFAULT NULL,
	"c_retail" float DEFAULT NULL,
	"c_rep_cost" float DEFAULT NULL,
	"c_shipping" float DEFAULT NULL,
	"c_app_rtl" float DEFAULT NULL,
	"c_app_cost" float DEFAULT NULL,
	"c_install" float DEFAULT NULL,
	"c_expecting" float DEFAULT NULL,
	"c_from_nw1" float DEFAULT NULL,
	"c_other_cost" float DEFAULT NULL,
	"c_from_other" float DEFAULT NULL,
	"c_business" float DEFAULT NULL,
	"user_id" integer DEFAULT NULL,
	"status" varchar(5) DEFAULT NULL,
	"order_id" integer DEFAULT NULL,
	"axia_invoice_number" integer DEFAULT NULL,
	"ach_seq_number" integer DEFAULT NULL,
	"description" varchar(200) DEFAULT NULL,
	"merchant_id" integer DEFAULT NULL,
	"split_commissions" boolean NOT NULL,
	"ref_seq_number" integer DEFAULT NULL,
	"res_seq_number" integer DEFAULT NULL,
	"partner_id" integer DEFAULT NULL,
	"partner_exclude_volume" boolean DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX commission_reports_merchant_id_idx ON "public"."commission_reports"("merchant_id");
CREATE INDEX cr_partner_id_index ON "public"."commission_reports"("partner_id");
CREATE INDEX crnew_mid_idx ON "public"."commission_reports"("merchant_id");

CREATE TABLE "public"."compliant_merchants_mvs" (
	"id" serial NOT NULL,
	"merchant_id" varchar(50) DEFAULT NULL,
	"merchant_email" varchar(100) DEFAULT NULL,
	"merchant_name" varchar(255) DEFAULT NULL,
	"password" varchar(255) DEFAULT NULL,
	"email_sent" timestamp ,
	"saq_type" varchar(4) DEFAULT NULL,
	"first_scan_date" date DEFAULT NULL,
	"first_questionnaire_date" date DEFAULT NULL,
	"scan_status" text DEFAULT NULL,
	"questionnaire_status" text DEFAULT NULL,
	"pci_compliance" varchar(3) DEFAULT NULL,
	"creation_date" date DEFAULT NULL,
	"sua" timestamp ,
	"saq_completed_date" date DEFAULT NULL,
	"billing_date" timestamp ,
	"next_billing_date" timestamp ,
	"quarterly_scan_fee" float DEFAULT NULL,
	"compliance_fee" float DEFAULT NULL,
	"insurance_fee" float DEFAULT NULL,
	"datecomplete" timestamp ,
	"saq_level" varchar(4) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX compliant_merchants_mvs_merchant_id_idx ON "public"."compliant_merchants_mvs"("merchant_id");

CREATE TABLE "public"."debit_acquirers" (
	"id" serial NOT NULL,
	"debit_acquirer_id" integer NOT NULL,
	"debit_acquirers" varchar(137) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."debits" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"mid" varchar(20) DEFAULT NULL,
	"transaction_fee" float DEFAULT NULL,
	"monthly_fee" float DEFAULT NULL,
	"monthly_volume" float DEFAULT NULL,
	"monthly_num_items" float DEFAULT NULL,
	"rate_pct" float DEFAULT NULL,
	"acquirer_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX debits_merchant_id_idx ON "public"."debits"("merchant_id");

CREATE TABLE "public"."discover_bets" (
	"id" serial NOT NULL,
	"bet_code" varchar(15) NOT NULL,
	"bet_extra_pct" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."discover_user_bet_tables" (
	"id" serial NOT NULL,
	"bet_code" varchar(15) NOT NULL,
	"user_id" integer NOT NULL,
	"network" varchar(20) NOT NULL,
	"rate" float DEFAULT NULL,
	"pi" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX discover_user_bet_table_index1 ON "public"."discover_user_bet_tables"("user_id");

CREATE TABLE "public"."discovers" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"bet_code" varchar(15) DEFAULT NULL,
	"disc_processing_rate" float DEFAULT NULL,
	"disc_per_item_fee" float DEFAULT NULL,
	"disc_statement_fee" float DEFAULT NULL,
	"disc_monthly_volume" float DEFAULT NULL,
	"disc_average_ticket" float DEFAULT NULL,
	"disc_risk_assessment" float DEFAULT NULL,
	"disc_gw_gateway_id" integer DEFAULT NULL,
	"disc_gw_rep_rate" float DEFAULT NULL,
	"disc_gw_rep_per_item" float DEFAULT NULL,
	"disc_gw_rep_features" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX discover_bet_index ON "public"."discovers"("bet_code");
CREATE INDEX discovers_merchant_id_idx ON "public"."discovers"("merchant_id");

CREATE TABLE "public"."ebts" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"mid" varchar(20) DEFAULT NULL,
	"transaction_fee" float DEFAULT NULL,
	"monthly_fee" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX ebts_merchant_id_idx ON "public"."ebts"("merchant_id");

CREATE TABLE "public"."entities" (
	"id" serial NOT NULL,
	"entity" varchar(10) NOT NULL,
	"entity_name" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."equipment_items" (
	"id" serial NOT NULL,
	"equipment_type" varchar(10) NOT NULL,
	"equipment_item_description" varchar(100) DEFAULT NULL,
	"equipment_item_true_price" float DEFAULT NULL,
	"equipment_item_rep_price" float DEFAULT NULL,
	"active" integer DEFAULT 1,
	"warranty" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX equipment_item_active ON "public"."equipment_items"("active");
CREATE INDEX equipment_item_type ON "public"."equipment_items"("equipment_type");
CREATE INDEX equipment_item_warranty ON "public"."equipment_items"("warranty");

CREATE TABLE "public"."equipment_programming_type_xrefs" (
	"id" serial NOT NULL,
	"equipment_programming_id" integer NOT NULL,
	"programming_type" varchar(10) NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX eptx_programming_idx ON "public"."equipment_programming_type_xrefs"("equipment_programming_id");

CREATE TABLE "public"."equipment_programmings" (
	"id" serial NOT NULL,
	"programming_id" integer NOT NULL,
	"merchant_id" integer NOT NULL,
	"terminal_number" varchar(20) DEFAULT NULL,
	"hardware_serial" varchar(20) DEFAULT NULL,
	"terminal_type" varchar(20) DEFAULT NULL,
	"network" varchar(20) DEFAULT NULL,
	"provider" varchar(20) DEFAULT NULL,
	"app_type" varchar(20) DEFAULT NULL,
	"status" varchar(5) DEFAULT NULL,
	"date_entered" date DEFAULT NULL,
	"date_changed" date DEFAULT NULL,
	"user_id" integer DEFAULT NULL,
	"serial_number" varchar(20) DEFAULT NULL,
	"pin_pad" varchar(20) DEFAULT NULL,
	"printer" varchar(20) DEFAULT NULL,
	"auto_close" varchar(20) DEFAULT NULL,
	"chain" varchar(6) DEFAULT NULL,
	"agent" varchar(6) DEFAULT NULL,
	"gateway_id" integer DEFAULT NULL,
	"version" varchar(20) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX equipment_programming_appid ON "public"."equipment_programmings"("app_type");
CREATE INDEX equipment_programming_merch_idx ON "public"."equipment_programmings"("merchant_id");
CREATE INDEX equipment_programming_serialnum ON "public"."equipment_programmings"("serial_number");
CREATE INDEX equipment_programming_status ON "public"."equipment_programmings"("status");
CREATE INDEX equipment_programming_userid ON "public"."equipment_programmings"("user_id");
CREATE INDEX equipment_programmings_merchant_id_idx ON "public"."equipment_programmings"("merchant_id");

CREATE TABLE "public"."equipment_types" (
	"id" serial NOT NULL,
	"equipment_type" varchar(10) NOT NULL,
	"equipment_type_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."gateway0" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"gw_rate" float DEFAULT NULL,
	"gw_per_item" float DEFAULT NULL,
	"gw_statement" float DEFAULT NULL,
	"gw_epay_retail_num" integer DEFAULT NULL,
	"gw_usaepay_rep_gtwy_cost_id" integer DEFAULT NULL,
	"gw_usaepay_rep_gtwy_add_cost_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX gateway0_merchant_id_idx ON "public"."gateway0"("merchant_id");
CREATE INDEX gateway_gw_usaepay_add_cost ON "public"."gateway0"("gw_usaepay_rep_gtwy_add_cost_id");
CREATE INDEX gateway_gw_usaepay_cost ON "public"."gateway0"("gw_usaepay_rep_gtwy_cost_id");

CREATE TABLE "public"."gateway1" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"gw1_rate" float DEFAULT NULL,
	"gw1_per_item" float DEFAULT NULL,
	"gw1_statement" float DEFAULT NULL,
	"gw1_rep_rate" float DEFAULT NULL,
	"gw1_rep_per_item" float DEFAULT NULL,
	"gw1_rep_statement" float DEFAULT NULL,
	"gw1_rep_features" text DEFAULT NULL,
	"gw1_gateway_id" integer NOT NULL,
	"gw1_monthly_volume" float DEFAULT NULL,
	"gw1_monthly_num_items" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX gateway1_merchant_id_idx ON "public"."gateway1"("merchant_id");
CREATE INDEX gw1_gatewayid ON "public"."gateway1"("gw1_gateway_id");

CREATE TABLE "public"."gateway2" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"gw2_rate" float DEFAULT NULL,
	"gw2_per_item" float DEFAULT NULL,
	"gw2_statement" float DEFAULT NULL,
	"gw2_rep_rate" float DEFAULT NULL,
	"gw2_rep_per_item" float DEFAULT NULL,
	"gw2_rep_statement" float DEFAULT NULL,
	"gw2_rep_features" text DEFAULT NULL,
	"gw2_gateway_id" integer NOT NULL,
	"gw2_monthly_volume" float DEFAULT NULL,
	"gw2_monthly_num_items" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX gateway2_merchant_id_idx ON "public"."gateway2"("merchant_id");
CREATE INDEX gw2_gatewayid ON "public"."gateway2"("gw2_gateway_id");

CREATE TABLE "public"."gateways" (
	"id" serial NOT NULL,
	"name" varchar(57) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."gift_cards" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"gc_mid" varchar(20) DEFAULT NULL,
	"gc_magstripe_loyalty_item_fee" float DEFAULT NULL,
	"gc_magstripe_gift_item_fee" float DEFAULT NULL,
	"gc_chip_card_one_rate" float DEFAULT NULL,
	"gc_chip_card_gift_item_fee" float DEFAULT NULL,
	"gc_chip_card_loyalty_item_fee" float DEFAULT NULL,
	"gc_smart_card_printing" float DEFAULT NULL,
	"gc_mag_card_printing" float DEFAULT NULL,
	"gc_loyalty_mgmt_database" float DEFAULT NULL,
	"gc_statement_fee" float DEFAULT NULL,
	"gc_application_fee" float DEFAULT NULL,
	"gc_equipment_fee" float DEFAULT NULL,
	"gc_misc_supplies" float DEFAULT NULL,
	"gc_merch_prov_art_setup_fee" float DEFAULT NULL,
	"gc_news_provider_artwork_fee" float DEFAULT NULL,
	"gc_training_fee" float DEFAULT NULL,
	"gc_plan" varchar(8) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX gift_cards_merchant_id_idx ON "public"."gift_cards"("merchant_id");

CREATE TABLE "public"."groups" (
	"id" serial NOT NULL,
	"group_id" varchar(10) NOT NULL,
	"group_description" varchar(50) DEFAULT NULL,
	"active" boolean DEFAULT 'TRUE' NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."last_deposit_reports" (
	"id" serial NOT NULL,
	"merchant_id" integer DEFAULT NULL,
	"merchant_dba" varchar(100) DEFAULT NULL,
	"last_deposit_date" date DEFAULT NULL,
	"user_id" integer DEFAULT NULL,
	"monthly_volume" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX last_deposit_reports_merchant_id_idx ON "public"."last_deposit_reports"("merchant_id");

CREATE TABLE "public"."merchant_ach_app_statuses" (
	"id" serial NOT NULL,
	"app_status_id" integer NOT NULL,
	"app_status_description" varchar(50) NOT NULL,
	"rank" integer DEFAULT NULL,
	"app_true_price" float DEFAULT NULL,
	"app_rep_price" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_ach_billing_options" (
	"id" serial NOT NULL,
	"billing_option_id" integer NOT NULL,
	"billing_option_description" varchar(50) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_achs" (
	"id" serial NOT NULL,
	"ach_seq_number" integer NOT NULL,
	"merchant_id" integer NOT NULL,
	"date_submitted" date DEFAULT NULL,
	"date_completed" date DEFAULT NULL,
	"reason" varchar(100) DEFAULT NULL,
	"credit_amount" float DEFAULT NULL,
	"debit_amount" float DEFAULT NULL,
	"reason_other" varchar(100) DEFAULT NULL,
	"status" varchar(5) DEFAULT NULL,
	"user_id" integer DEFAULT NULL,
	"invoice_number" integer DEFAULT NULL,
	"app_status_id" integer DEFAULT NULL,
	"billing_option_id" integer DEFAULT NULL,
	"commission_month" varchar(8) DEFAULT NULL,
	"tax" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_ach_idx ON "public"."merchant_achs"("merchant_id");
CREATE INDEX merchant_achs_merchant_id_idx ON "public"."merchant_achs"("merchant_id");

CREATE TABLE "public"."merchant_acquirers" (
	"id" serial NOT NULL,
	"acquirer_id" integer NOT NULL,
	"acquirer" varchar(21) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_banks" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"bank_routing_number" varchar(255) DEFAULT NULL,
	"bank_dda_number" varchar(255) DEFAULT NULL,
	"bank_name" varchar(60) DEFAULT NULL,
	"bank_routing_number_disp" varchar(4) DEFAULT NULL,
	"bank_dda_number_disp" varchar(4) DEFAULT NULL,
	"fees_routing_number" varchar(255) DEFAULT NULL,
	"fees_dda_number" varchar(255) DEFAULT NULL,
	"fees_routing_number_disp" varchar(4) DEFAULT NULL,
	"fees_dda_number_disp" varchar(4) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_banks_merchant_id_idx ON "public"."merchant_banks"("merchant_id");

CREATE TABLE "public"."merchant_bins" (
	"id" serial NOT NULL,
	"bin_id" integer NOT NULL,
	"bin" varchar(21) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_cancellation_subreasons" (
	"id" serial NOT NULL,
	"name" varchar(65) NOT NULL,
	"visible" boolean DEFAULT 'TRUE' NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_cancellations" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"date_submitted" date DEFAULT NULL,
	"date_completed" date DEFAULT NULL,
	"fee_charged" float DEFAULT NULL,
	"reason" text DEFAULT NULL,
	"status" varchar(5) DEFAULT 'PEND' NOT NULL,
	"axia_invoice_number" integer DEFAULT NULL,
	"date_inactive" date DEFAULT NULL,
	"merchant_cancellation_subreason" varchar(255) DEFAULT NULL,
	"subreason_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_cancellation_index1 ON "public"."merchant_cancellations"("date_submitted");
CREATE INDEX merchant_cancellation_index2 ON "public"."merchant_cancellations"("date_completed");
CREATE INDEX merchant_cancellation_index3 ON "public"."merchant_cancellations"("status");
CREATE INDEX merchant_cancellation_index9 ON "public"."merchant_cancellations"("date_inactive");
CREATE INDEX merchant_cancellation_index99 ON "public"."merchant_cancellations"("subreason_id");
CREATE INDEX merchant_cancellations_merchant_id_idx ON "public"."merchant_cancellations"("merchant_id");

CREATE TABLE "public"."merchant_card_types" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"card_type" varchar(10) NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX mct_merchant_idx ON "public"."merchant_card_types"("merchant_id");
CREATE INDEX merchant_card_types_merchant_id_idx ON "public"."merchant_card_types"("merchant_id");

CREATE TABLE "public"."merchant_changes" (
	"id" serial NOT NULL,
	"change_id" integer NOT NULL,
	"change_type" integer NOT NULL,
	"merchant_id" integer NOT NULL,
	"user_id" integer NOT NULL,
	"status" varchar(5) NOT NULL,
	"date_entered" date NOT NULL,
	"time_entered" time NOT NULL,
	"date_approved" date DEFAULT NULL,
	"time_approved" time DEFAULT NULL,
	"change_data" text DEFAULT NULL,
	"merchant_note_id" integer DEFAULT NULL,
	"approved_by" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_change_merchant_idx ON "public"."merchant_changes"("merchant_id");
CREATE INDEX merchant_changes_merchant_id_idx ON "public"."merchant_changes"("merchant_id");

CREATE TABLE "public"."merchant_notes" (
	"id" serial NOT NULL,
	"note_type" varchar(10) NOT NULL,
	"user_id" integer NOT NULL,
	"merchant_id" integer NOT NULL,
	"note_date" date DEFAULT NULL,
	"note" text DEFAULT NULL,
	"note_title" varchar(200) DEFAULT NULL,
	"general_status" varchar(5) DEFAULT NULL,
	"date_changed" date DEFAULT NULL,
	"critical" integer DEFAULT NULL,
	"note_sent" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_note_merchant_idx ON "public"."merchant_notes"("merchant_id");
CREATE INDEX merchant_notes_merchant_id_idx ON "public"."merchant_notes"("merchant_id");

CREATE TABLE "public"."merchant_owners" (
	"id" serial NOT NULL,
	"owner_id" integer NOT NULL,
	"merchant_id" integer NOT NULL,
	"owner_social_sec_no" varchar(255) DEFAULT NULL,
	"owner_equity" integer DEFAULT NULL,
	"owner_name" varchar(100) DEFAULT NULL,
	"owner_title" varchar(40) DEFAULT NULL,
	"owner_social_sec_no_disp" varchar(4) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_owner_merchant_idx ON "public"."merchant_owners"("merchant_id");
CREATE INDEX merchant_owners_merchant_id_idx ON "public"."merchant_owners"("merchant_id");

CREATE TABLE "public"."merchant_pcis" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"compliance_level" integer DEFAULT 4 NOT NULL,
	"saq_completed_date" date DEFAULT NULL,
	"compliance_fee" float DEFAULT NULL,
	"insurance_fee" float DEFAULT NULL,
	"last_security_scan" date DEFAULT NULL,
	"scanning_company" varchar(255) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_pcis_merchant_id_idx ON "public"."merchant_pcis"("merchant_id");
CREATE INDEX mp_compliance_fee ON "public"."merchant_pcis"("compliance_fee");
CREATE INDEX mp_insurance_fee ON "public"."merchant_pcis"("insurance_fee");
CREATE INDEX mp_saq_completed_date ON "public"."merchant_pcis"("saq_completed_date");

CREATE TABLE "public"."merchant_reference_types" (
	"id" serial NOT NULL,
	"merchant_ref_type" varchar(10) NOT NULL,
	"merchant_ref_type_desc" varchar(50) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_references" (
	"id" serial NOT NULL,
	"merchant_ref_seq_number" integer NOT NULL,
	"merchant_id" integer NOT NULL,
	"merchant_ref_type" varchar(10) NOT NULL,
	"bank_name" varchar(100) DEFAULT NULL,
	"person_name" varchar(100) DEFAULT NULL,
	"phone" varchar(20) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_reference_mid ON "public"."merchant_references"("merchant_id");
CREATE INDEX merchant_references_merchant_id_idx ON "public"."merchant_references"("merchant_id");

CREATE TABLE "public"."merchant_reject_lines" (
	"id" serial NOT NULL,
	"rejectid" integer NOT NULL,
	"fee" float DEFAULT NULL,
	"statusid" integer NOT NULL,
	"status_date" date DEFAULT NULL,
	"notes" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX mr_status_date_index1 ON "public"."merchant_reject_lines"("status_date");
CREATE INDEX mrl_rejectid_index1 ON "public"."merchant_reject_lines"("rejectid");
CREATE INDEX mrl_statusid_index1 ON "public"."merchant_reject_lines"("statusid");

CREATE TABLE "public"."merchant_reject_recurrances" (
	"id" serial NOT NULL,
	"name" varchar(57) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_reject_statuses" (
	"id" serial NOT NULL,
	"name" varchar(57) NOT NULL,
	"collected" boolean DEFAULT NULL,
	"priority" integer DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX mrs_collected_index1 ON "public"."merchant_reject_statuses"("collected");

CREATE TABLE "public"."merchant_reject_types" (
	"id" serial NOT NULL,
	"name" varchar(57) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_rejects" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"trace" integer NOT NULL,
	"reject_date" date NOT NULL,
	"typeid" integer NOT NULL,
	"code" varchar(9) NOT NULL,
	"amount" float DEFAULT 0 NOT NULL,
	"recurranceid" integer NOT NULL,
	"open" boolean DEFAULT 'TRUE' NOT NULL,
	"loss_axia" float DEFAULT NULL,
	"loss_mgr1" float DEFAULT NULL,
	"loss_mgr2" float DEFAULT NULL,
	"loss_rep" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX mr_trace_index1 ON "public"."merchant_rejects"("trace");
CREATE INDEX merchant_rejects_merchant_id_idx ON "public"."merchant_rejects"("merchant_id");
CREATE INDEX mr_merchant_id_index1 ON "public"."merchant_rejects"("merchant_id");
CREATE INDEX mr_open_index1 ON "public"."merchant_rejects"("open");
CREATE INDEX mr_recurranceid_index1 ON "public"."merchant_rejects"("recurranceid");
CREATE INDEX mr_reject_date_index1 ON "public"."merchant_rejects"("reject_date");
CREATE INDEX mr_typeid_index1 ON "public"."merchant_rejects"("typeid");

CREATE TABLE "public"."merchant_uw_final_approveds" (
	"id" serial NOT NULL,
	"name" varchar(21) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_uw_final_statuses" (
	"id" serial NOT NULL,
	"name" varchar(21) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."merchant_uws" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"tier_assignment" integer DEFAULT NULL,
	"credit_pct" float DEFAULT NULL,
	"chargeback_pct" float DEFAULT NULL,
	"final_status_id" integer DEFAULT NULL,
	"final_approved_id" integer DEFAULT NULL,
	"final_date" date DEFAULT NULL,
	"final_notes" text DEFAULT NULL,
	"mcc" varchar(255) DEFAULT NULL,
	"expedited" boolean NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX merchant_uw_expedited_index ON "public"."merchant_uws"("expedited");
CREATE INDEX merchant_uw_final_approved_id_index ON "public"."merchant_uws"("final_approved_id");
CREATE INDEX merchant_uw_final_date_index ON "public"."merchant_uws"("final_date");
CREATE INDEX merchant_uw_final_status_id_index ON "public"."merchant_uws"("final_status_id");
CREATE INDEX merchant_uw_tier_assignment_index ON "public"."merchant_uws"("tier_assignment");
CREATE INDEX merchant_uws_merchant_id_idx ON "public"."merchant_uws"("merchant_id");

CREATE TABLE "public"."merchants" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"user_id" integer NOT NULL,
	"merchant_mid" varchar(20) NOT NULL,
	"merchant_name" varchar(100) DEFAULT NULL,
	"merchant_dba" varchar(100) DEFAULT NULL,
	"merchant_contact" varchar(50) DEFAULT NULL,
	"merchant_email" varchar(50) DEFAULT NULL,
	"merchant_ownership_type" varchar(30) DEFAULT NULL,
	"merchant_tin" varchar(255) DEFAULT NULL,
	"merchant_d_and_b" varchar(255) DEFAULT NULL,
	"inactive_date" date DEFAULT NULL,
	"active_date" date DEFAULT NULL,
	"ref_seq_number" integer DEFAULT NULL,
	"network_id" integer DEFAULT NULL,
	"merchant_buslevel" varchar(50) DEFAULT NULL,
	"merchant_sic" integer DEFAULT NULL,
	"entity" varchar(10) DEFAULT NULL,
	"group_id" integer DEFAULT NULL,
	"merchant_bustype" varchar(100) DEFAULT NULL,
	"merchant_url" varchar(100) DEFAULT NULL,
	"merchant_tin_disp" varchar(4) DEFAULT NULL,
	"merchant_d_and_b_disp" varchar(4) DEFAULT NULL,
	"active" integer DEFAULT NULL,
	"cancellation_fee_id" integer DEFAULT NULL,
	"merchant_contact_position" varchar(50) DEFAULT NULL,
	"merchant_mail_contact" varchar(50) DEFAULT NULL,
	"res_seq_number" integer DEFAULT NULL,
	"merchant_bin_id" integer DEFAULT NULL,
	"merchant_acquirer_id" integer DEFAULT NULL,
	"reporting_user" varchar(65) DEFAULT NULL,
	"merchant_ps_sold" varchar(100) DEFAULT NULL,
	"ref_p_type" text DEFAULT NULL,
	"ref_p_value" float DEFAULT NULL,
	"res_p_type" text DEFAULT NULL,
	"res_p_value" float DEFAULT NULL,
	"ref_p_pct" integer DEFAULT NULL,
	"res_p_pct" integer DEFAULT NULL,
	"onlineapp_application_id" integer DEFAULT NULL,
	"partner_id" integer DEFAULT NULL,
	"partner_exclude_volume" boolean DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX merchants_merchant_id_idx ON "public"."merchants"("merchant_id");
CREATE INDEX merchant_active ON "public"."merchants"("active");
CREATE INDEX merchant_dba_idx ON "public"."merchants"("merchant_dba");
CREATE INDEX merchant_dbalower_idx ON "public"."merchants"("merchant_dba");
CREATE INDEX merchant_entity ON "public"."merchants"("entity");
CREATE INDEX merchant_groupid ON "public"."merchants"("group_id");
CREATE INDEX merchant_merchant_id_idx ON "public"."merchants"("merchant_id");
CREATE INDEX merchant_netid ON "public"."merchants"("network_id");
CREATE INDEX merchant_userid ON "public"."merchants"("user_id");
CREATE INDEX mpid_index1 ON "public"."merchants"("partner_id");

CREATE TABLE "public"."networks" (
	"id" serial NOT NULL,
	"network_id" integer NOT NULL,
	"network_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."non_compliant_merchants_cs_mv" (
	"id" serial NOT NULL,
	"merchant_id" varchar(50) DEFAULT NULL,
	"merchant_email" varchar(100) DEFAULT NULL,
	"merchant_name" varchar(255) DEFAULT NULL,
	"password" varchar(255) DEFAULT NULL,
	"email_sent" timestamp ,
	"saq_type" varchar(4) DEFAULT NULL,
	"first_scan_date" date DEFAULT NULL,
	"first_questionnaire_date" date DEFAULT NULL,
	"scan_status" text DEFAULT NULL,
	"questionnaire_status" text DEFAULT NULL,
	"pci_compliance" varchar(3) DEFAULT NULL,
	"creation_date" date DEFAULT NULL,
	"sua" timestamp ,
	"saq_completed_date" date DEFAULT NULL,
	"billing_date" timestamp ,
	"next_billing_date" timestamp ,
	"quarterly_scan_fee" float DEFAULT NULL,
	"compliance_fee" float DEFAULT NULL,
	"insurance_fee" float DEFAULT NULL
);
CREATE INDEX non_compliant_merchants_cs_mv_merchant_id_idx ON "public"."non_compliant_merchants_cs_mv"("merchant_id");

CREATE TABLE "public"."non_compliant_merchants_mv" (
	"id" serial NOT NULL,
	"merchant_id" varchar(50) DEFAULT NULL,
	"merchant_email" varchar(100) DEFAULT NULL,
	"merchant_name" varchar(255) DEFAULT NULL,
	"password" varchar(255) DEFAULT NULL,
	"email_sent" timestamp ,
	"saq_type" varchar(4) DEFAULT NULL,
	"first_scan_date" date DEFAULT NULL,
	"first_questionnaire_date" date DEFAULT NULL,
	"scan_status" text DEFAULT NULL,
	"questionnaire_status" text DEFAULT NULL,
	"pci_compliance" varchar(3) DEFAULT NULL,
	"creation_date" date DEFAULT NULL,
	"sua" timestamp ,
	"saq_completed_date" date DEFAULT NULL,
	"billing_date" timestamp ,
	"next_billing_date" timestamp ,
	"quarterly_scan_fee" float DEFAULT NULL,
	"compliance_fee" float DEFAULT NULL,
	"insurance_fee" float DEFAULT NULL
);
CREATE INDEX non_compliant_merchants_mv_merchant_id_idx ON "public"."non_compliant_merchants_mv"("merchant_id");

CREATE TABLE "public"."note_types" (
	"id" serial NOT NULL,
	"note_type" varchar(10) NOT NULL,
	"note_type_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_acos" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT NULL,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT NULL,
	"alias" varchar(255) DEFAULT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_apips" (
	"id" serial NOT NULL,
	"user_id" integer DEFAULT NULL,
	"ip_address" inet DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_applications" (
	"id" serial NOT NULL,
	"user_id" integer DEFAULT NULL,
	"status" varchar(10) DEFAULT NULL,
	"hash" varchar(32) DEFAULT NULL,
	"rs_document_guid" varchar(40) DEFAULT NULL,
	"ownership_type" varchar(255) DEFAULT NULL,
	"legal_business_name" varchar(255) DEFAULT NULL,
	"mailing_address" varchar(255) DEFAULT NULL,
	"mailing_city" varchar(255) DEFAULT NULL,
	"mailing_state" varchar(255) DEFAULT NULL,
	"mailing_zip" varchar(10) DEFAULT NULL,
	"mailing_phone" varchar(20) DEFAULT NULL,
	"mailing_fax" varchar(20) DEFAULT NULL,
	"federal_taxid" varchar(255) DEFAULT NULL,
	"corp_contact_name" varchar(255) DEFAULT NULL,
	"corp_contact_name_title" varchar(20) DEFAULT NULL,
	"corporate_email" varchar(255) DEFAULT NULL,
	"loc_same_as_corp" boolean DEFAULT NULL,
	"dba_business_name" varchar(255) DEFAULT NULL,
	"location_address" varchar(255) DEFAULT NULL,
	"location_city" varchar(255) DEFAULT NULL,
	"location_state" varchar(255) DEFAULT NULL,
	"location_zip" varchar(10) DEFAULT NULL,
	"location_phone" varchar(20) DEFAULT NULL,
	"location_fax" varchar(20) DEFAULT NULL,
	"customer_svc_phone" varchar(20) DEFAULT NULL,
	"loc_contact_name" varchar(255) DEFAULT NULL,
	"loc_contact_name_title" varchar(255) DEFAULT NULL,
	"location_email" varchar(255) DEFAULT NULL,
	"website" varchar(255) DEFAULT NULL,
	"bus_open_date" varchar(255) DEFAULT NULL,
	"length_current_ownership" varchar(255) DEFAULT NULL,
	"existing_axia_merchant" varchar(10) DEFAULT NULL,
	"current_mid_number" varchar(50) DEFAULT NULL,
	"general_comments" text DEFAULT NULL,
	"location_type" varchar(255) DEFAULT NULL,
	"location_type_other" varchar(255) DEFAULT NULL,
	"merchant_status" varchar(255) DEFAULT NULL,
	"landlord_name" varchar(255) DEFAULT NULL,
	"landlord_phone" varchar(255) DEFAULT NULL,
	"business_type" varchar(50) DEFAULT NULL,
	"products_services_sold" varchar(255) DEFAULT NULL,
	"return_policy" varchar(255) DEFAULT NULL,
	"days_until_prod_delivery" varchar(255) DEFAULT NULL,
	"monthly_volume" varchar(50) DEFAULT NULL,
	"average_ticket" varchar(50) DEFAULT NULL,
	"highest_ticket" varchar(50) DEFAULT NULL,
	"current_processor" varchar(50) DEFAULT NULL,
	"card_present_swiped" integer DEFAULT NULL,
	"card_present_imprint" integer DEFAULT NULL,
	"card_not_present_keyed" integer DEFAULT NULL,
	"card_not_present_internet" integer DEFAULT NULL,
	"method_total" integer DEFAULT NULL,
	"direct_to_customer" integer DEFAULT NULL,
	"direct_to_business" integer DEFAULT NULL,
	"direct_to_govt" integer DEFAULT NULL,
	"products_total" integer DEFAULT NULL,
	"high_volume_january" boolean DEFAULT NULL,
	"high_volume_february" boolean DEFAULT NULL,
	"high_volume_march" boolean DEFAULT NULL,
	"high_volume_april" boolean DEFAULT NULL,
	"high_volume_may" boolean DEFAULT NULL,
	"high_volume_june" boolean DEFAULT NULL,
	"high_volume_july" boolean DEFAULT NULL,
	"high_volume_august" boolean DEFAULT NULL,
	"high_volume_september" boolean DEFAULT NULL,
	"high_volume_october" boolean DEFAULT NULL,
	"high_volume_november" boolean DEFAULT NULL,
	"high_volume_december" boolean DEFAULT NULL,
	"moto_storefront_location" varchar(10) DEFAULT NULL,
	"moto_orders_at_location" varchar(10) DEFAULT NULL,
	"moto_inventory_housed" varchar(255) DEFAULT NULL,
	"moto_outsourced_customer_service" boolean DEFAULT NULL,
	"moto_outsourced_shipment" boolean DEFAULT NULL,
	"moto_outsourced_returns" boolean DEFAULT NULL,
	"moto_outsourced_billing" boolean DEFAULT NULL,
	"moto_sales_methods" varchar(255) DEFAULT NULL,
	"moto_billing_monthly" boolean DEFAULT NULL,
	"moto_billing_quarterly" boolean DEFAULT NULL,
	"moto_billing_semiannually" boolean DEFAULT NULL,
	"moto_billing_annually" boolean DEFAULT NULL,
	"moto_policy_full_up_front" varchar(10) DEFAULT NULL,
	"moto_policy_days_until_delivery" varchar(10) DEFAULT NULL,
	"moto_policy_partial_up_front" varchar(10) DEFAULT NULL,
	"moto_policy_partial_with" varchar(10) DEFAULT NULL,
	"moto_policy_days_until_final" varchar(10) DEFAULT NULL,
	"moto_policy_after" varchar(10) DEFAULT NULL,
	"bank_name" varchar(255) DEFAULT NULL,
	"bank_contact_name" varchar(255) DEFAULT NULL,
	"bank_phone" varchar(20) DEFAULT NULL,
	"bank_address" varchar(255) DEFAULT NULL,
	"bank_city" varchar(255) DEFAULT NULL,
	"bank_state" varchar(255) DEFAULT NULL,
	"bank_zip" varchar(20) DEFAULT NULL,
	"depository_routing_number" varchar(255) DEFAULT NULL,
	"depository_account_number" varchar(255) DEFAULT NULL,
	"same_as_depository" boolean DEFAULT NULL,
	"fees_routing_number" varchar(255) DEFAULT NULL,
	"fees_account_number" varchar(255) DEFAULT NULL,
	"trade1_business_name" varchar(255) DEFAULT NULL,
	"trade1_contact_person" varchar(255) DEFAULT NULL,
	"trade1_phone" varchar(20) DEFAULT NULL,
	"trade1_acct_num" varchar(255) DEFAULT NULL,
	"trade1_city" varchar(255) DEFAULT NULL,
	"trade1_state" varchar(255) DEFAULT NULL,
	"trade2_business_name" varchar(255) DEFAULT NULL,
	"trade2_contact_person" varchar(255) DEFAULT NULL,
	"trade2_phone" varchar(20) DEFAULT NULL,
	"trade2_acct_num" varchar(255) DEFAULT NULL,
	"trade2_city" varchar(255) DEFAULT NULL,
	"trade2_state" varchar(255) DEFAULT NULL,
	"currently_accept_amex" varchar(10) DEFAULT NULL,
	"existing_se_num" varchar(255) DEFAULT NULL,
	"want_to_accept_amex" varchar(10) DEFAULT NULL,
	"want_to_accept_discover" varchar(10) DEFAULT NULL,
	"term1_quantity" integer DEFAULT NULL,
	"term1_type" varchar(20) DEFAULT NULL,
	"term1_provider" varchar(255) DEFAULT NULL,
	"term1_use_autoclose" varchar(10) DEFAULT NULL,
	"term1_what_time" varchar(255) DEFAULT NULL,
	"term1_programming_avs" boolean DEFAULT NULL,
	"term1_programming_server_nums" boolean DEFAULT NULL,
	"term1_programming_tips" boolean DEFAULT NULL,
	"term1_programming_invoice_num" boolean DEFAULT NULL,
	"term1_programming_purchasing_cards" boolean DEFAULT NULL,
	"term1_accept_debit" varchar(10) DEFAULT NULL,
	"term1_pin_pad_type" varchar(255) DEFAULT NULL,
	"term1_pin_pad_qty" integer DEFAULT NULL,
	"term2_quantity" integer DEFAULT NULL,
	"term2_type" varchar(20) DEFAULT NULL,
	"term2_provider" varchar(255) DEFAULT NULL,
	"term2_use_autoclose" varchar(10) DEFAULT NULL,
	"term2_what_time" varchar(255) DEFAULT NULL,
	"term2_programming_avs" boolean DEFAULT NULL,
	"term2_programming_server_nums" boolean DEFAULT NULL,
	"term2_programming_tips" boolean DEFAULT NULL,
	"term2_programming_invoice_num" boolean DEFAULT NULL,
	"term2_programming_purchasing_cards" boolean DEFAULT NULL,
	"term2_accept_debit" varchar(10) DEFAULT NULL,
	"term2_pin_pad_type" varchar(255) DEFAULT NULL,
	"term2_pin_pad_qty" integer DEFAULT NULL,
	"owner1_percentage" integer DEFAULT NULL,
	"owner1_fullname" varchar(255) DEFAULT NULL,
	"owner1_title" varchar(255) DEFAULT NULL,
	"owner1_address" varchar(255) DEFAULT NULL,
	"owner1_city" varchar(255) DEFAULT NULL,
	"owner1_state" varchar(255) DEFAULT NULL,
	"owner1_zip" varchar(20) DEFAULT NULL,
	"owner1_phone" varchar(20) DEFAULT NULL,
	"owner1_fax" varchar(20) DEFAULT NULL,
	"owner1_email" varchar(255) DEFAULT NULL,
	"owner1_ssn" varchar(255) DEFAULT NULL,
	"owner1_dob" varchar(50) DEFAULT NULL,
	"owner2_percentage" integer DEFAULT NULL,
	"owner2_fullname" varchar(255) DEFAULT NULL,
	"owner2_title" varchar(255) DEFAULT NULL,
	"owner2_address" varchar(255) DEFAULT NULL,
	"owner2_city" varchar(255) DEFAULT NULL,
	"owner2_state" varchar(255) DEFAULT NULL,
	"owner2_zip" varchar(20) DEFAULT NULL,
	"owner2_phone" varchar(20) DEFAULT NULL,
	"owner2_fax" varchar(20) DEFAULT NULL,
	"owner2_email" varchar(255) DEFAULT NULL,
	"owner2_ssn" varchar(255) DEFAULT NULL,
	"owner2_dob" varchar(50) DEFAULT NULL,
	"referral1_business" varchar(255) DEFAULT NULL,
	"referral1_owner_officer" varchar(255) DEFAULT NULL,
	"referral1_phone" varchar(20) DEFAULT NULL,
	"referral2_business" varchar(255) DEFAULT NULL,
	"referral2_owner_officer" varchar(255) DEFAULT NULL,
	"referral2_phone" varchar(20) DEFAULT NULL,
	"referral3_business" varchar(255) DEFAULT NULL,
	"referral3_owner_officer" varchar(255) DEFAULT NULL,
	"referral3_phone" varchar(20) DEFAULT NULL,
	"rep_contractor_name" varchar(255) DEFAULT NULL,
	"fees_rate_discount" varchar(20) DEFAULT NULL,
	"fees_rate_structure" varchar(255) DEFAULT NULL,
	"fees_qualification_exemptions" varchar(255) DEFAULT NULL,
	"fees_startup_application" varchar(20) DEFAULT NULL,
	"fees_auth_transaction" varchar(20) DEFAULT NULL,
	"fees_monthly_statement" varchar(20) DEFAULT NULL,
	"fees_misc_annual_file" varchar(20) DEFAULT NULL,
	"fees_startup_equipment" varchar(20) DEFAULT NULL,
	"fees_auth_amex" varchar(20) DEFAULT NULL,
	"fees_monthly_minimum" varchar(20) DEFAULT NULL,
	"fees_misc_chargeback" varchar(20) DEFAULT NULL,
	"fees_startup_expedite" varchar(20) DEFAULT NULL,
	"fees_auth_aru_voice" varchar(20) DEFAULT NULL,
	"fees_monthly_debit_access" varchar(20) DEFAULT NULL,
	"fees_startup_reprogramming" varchar(20) DEFAULT NULL,
	"fees_auth_wireless" varchar(20) DEFAULT NULL,
	"fees_monthly_ebt" varchar(20) DEFAULT NULL,
	"fees_startup_training" varchar(20) DEFAULT NULL,
	"fees_monthly_gateway_access" varchar(20) DEFAULT NULL,
	"fees_startup_wireless_activation" varchar(20) DEFAULT NULL,
	"fees_monthly_wireless_access" varchar(20) DEFAULT NULL,
	"fees_startup_tax" varchar(20) DEFAULT NULL,
	"fees_startup_total" varchar(20) DEFAULT NULL,
	"fees_pin_debit_auth" varchar(20) DEFAULT NULL,
	"fees_ebt_discount" varchar(20) DEFAULT NULL,
	"fees_pin_debit_discount" varchar(20) DEFAULT NULL,
	"fees_ebt_auth" varchar(20) DEFAULT NULL,
	"rep_discount_paid" varchar(10) DEFAULT NULL,
	"rep_amex_discount_rate" varchar(20) DEFAULT NULL,
	"rep_business_legitimate" varchar(10) DEFAULT NULL,
	"rep_photo_included" varchar(10) DEFAULT NULL,
	"rep_inventory_sufficient" varchar(10) DEFAULT NULL,
	"rep_goods_delivered" varchar(10) DEFAULT NULL,
	"rep_bus_open_operating" varchar(10) DEFAULT NULL,
	"rep_visa_mc_decals_visible" varchar(10) DEFAULT NULL,
	"rep_mail_tel_activity" varchar(10) DEFAULT NULL,
	"created" timestamp ,
	"modified" timestamp ,
	"moto_inventory_owned" varchar(10) DEFAULT NULL,
	"moto_outsourced_customer_service_field" varchar(40) DEFAULT NULL,
	"moto_outsourced_shipment_field" varchar(40) DEFAULT NULL,
	"moto_outsourced_returns_field" varchar(40) DEFAULT NULL,
	"moto_sales_local" boolean DEFAULT NULL,
	"moto_sales_national" boolean DEFAULT NULL,
	"site_survey_signature" varchar(40) DEFAULT NULL,
	"api" integer DEFAULT 0 NOT NULL,
	"var_status" varchar(10) DEFAULT NULL,
	"install_var_rs_document_guid" varchar(32) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_aros" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT NULL,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT NULL,
	"alias" varchar(255) DEFAULT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_aros_acos" (
	"id" serial NOT NULL,
	"aro_id" integer NOT NULL,
	"aco_id" integer NOT NULL,
	"_create" varchar(2) DEFAULT '0' NOT NULL,
	"_read" varchar(2) DEFAULT '0' NOT NULL,
	"_update" varchar(2) DEFAULT '0' NOT NULL,
	"_delete" varchar(2) DEFAULT '0' NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_email_timeline_subjects" (
	"id" serial NOT NULL,
	"subject" varchar(40) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_email_timelines" (
	"id" serial NOT NULL,
	"onlineapp_application_id" integer DEFAULT NULL,
	"date" timestamp ,
	"subject_id" integer DEFAULT NULL,
	"recipient" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_epayments" (
	"id" serial NOT NULL,
	"pin" integer NOT NULL,
	"application_id" integer NOT NULL,
	"merchant_id" integer DEFAULT NULL,
	"user_id" integer DEFAULT NULL,
	"onlineapp_applications_id" integer DEFAULT NULL,
	"date_boarded" timestamp NOT NULL,
	"date_retrieved" timestamp ,
	PRIMARY KEY  ("id")
);
CREATE INDEX onlineapp_epayments_merchant_id_idx ON "public"."onlineapp_epayments"("merchant_id");

CREATE TABLE "public"."onlineapp_groups" (
	"id" serial NOT NULL,
	"name" varchar(100) NOT NULL,
	"created" timestamp ,
	"modified" timestamp ,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_settings" (
	"id" serial NOT NULL,
	"key" varchar(255) NOT NULL,
	"value" varchar(255) DEFAULT NULL,
	"description" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."onlineapp_users" (
	"id" serial NOT NULL,
	"email" varchar(255) NOT NULL,
	"password" varchar(40) NOT NULL,
	"group_id" integer NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"token" varchar(40) DEFAULT NULL,
	"token_used" timestamp ,
	"token_uses" integer DEFAULT 0 NOT NULL,
	"firstname" varchar(40) DEFAULT NULL,
	"lastname" varchar(40) DEFAULT NULL,
	"extension" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."orderitem_types" (
	"id" serial NOT NULL,
	"orderitem_type_id" integer NOT NULL,
	"orderitem_type_description" varchar(55) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."orderitems" (
	"id" serial NOT NULL,
	"orderitem_id" integer NOT NULL,
	"order_id" integer NOT NULL,
	"equipment_type" varchar(10) NOT NULL,
	"equipment_item_description" varchar(100) DEFAULT NULL,
	"quantity" integer DEFAULT NULL,
	"equipment_item_true_price" float DEFAULT NULL,
	"equipment_item_rep_price" float DEFAULT NULL,
	"equipment_item" integer DEFAULT NULL,
	"hardware_sn" varchar(20) DEFAULT NULL,
	"hardware_replacement_for" varchar(20) DEFAULT NULL,
	"warranty" integer DEFAULT NULL,
	"item_merchant_id" varchar(50) DEFAULT NULL,
	"item_tax" float DEFAULT NULL,
	"item_ship_type" integer DEFAULT NULL,
	"item_ship_cost" float DEFAULT NULL,
	"item_commission_month" varchar(8) DEFAULT NULL,
	"item_ach_seq" integer DEFAULT NULL,
	"item_date_ordered" date DEFAULT NULL,
	"type_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX orderitems_eqtype ON "public"."orderitems"("equipment_type");
CREATE INDEX orderitems_itemmerchid ON "public"."orderitems"("item_merchant_id");
CREATE INDEX orderitems_order_idx ON "public"."orderitems"("order_id");
CREATE INDEX orderitems_typeid ON "public"."orderitems"("type_id");

CREATE TABLE "public"."orderitems_replacements" (
	"id" serial NOT NULL,
	"orderitem_replacement_id" integer NOT NULL,
	"orderitem_id" integer NOT NULL,
	"shipping_axia_to_merchant_id" integer DEFAULT NULL,
	"shipping_axia_to_merchant_cost" float DEFAULT NULL,
	"shipping_merchant_to_vendor_id" integer DEFAULT NULL,
	"shipping_merchant_to_vendor_cos" float DEFAULT NULL,
	"shipping_vendor_to_axia_id" integer DEFAULT NULL,
	"shipping_vendor_to_axia_cost" float DEFAULT NULL,
	"ra_num" integer DEFAULT NULL,
	"tracking_num_old" integer DEFAULT NULL,
	"date_shipped_to_vendor" date DEFAULT NULL,
	"date_arrived_from_vendor" date DEFAULT NULL,
	"amount_billed_to_merchant" float DEFAULT NULL,
	"tracking_num" varchar(25) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX orderitems_replacement_oid ON "public"."orderitems_replacements"("orderitem_id");

CREATE TABLE "public"."orders" (
	"id" serial NOT NULL,
	"order_id" integer NOT NULL,
	"status" varchar(5) NOT NULL,
	"user_id" integer NOT NULL,
	"date_ordered" date DEFAULT NULL,
	"date_paid" date DEFAULT NULL,
	"merchant_id" integer DEFAULT NULL,
	"shipping_cost" float DEFAULT NULL,
	"tax" float DEFAULT NULL,
	"ship_to" varchar(50) DEFAULT NULL,
	"tracking_number" varchar(50) DEFAULT NULL,
	"notes" text DEFAULT NULL,
	"invoice_number" integer DEFAULT NULL,
	"shipping_type" integer DEFAULT NULL,
	"display_id" integer DEFAULT NULL,
	"ach_seq_number" integer DEFAULT NULL,
	"commission_month" varchar(8) DEFAULT NULL,
	"vendor_id" integer DEFAULT NULL,
	"add_item_tax" integer DEFAULT NULL,
	"commission_month_nocharge" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX orders_commissionmonth ON "public"."orders"("commission_month");
CREATE INDEX orders_invnum ON "public"."orders"("invoice_number");
CREATE INDEX orders_merchant_id_idx ON "public"."orders"("merchant_id");
CREATE INDEX orders_merchant_idx ON "public"."orders"("merchant_id");
CREATE INDEX orders_shiptype ON "public"."orders"("shipping_type");
CREATE INDEX orders_status ON "public"."orders"("status");
CREATE INDEX orders_user_idx ON "public"."orders"("user_id");
CREATE INDEX orders_vendorid ON "public"."orders"("vendor_id");

CREATE TABLE "public"."partners" (
	"id" serial NOT NULL,
	"partner_id" integer NOT NULL,
	"partner_name" varchar(255) NOT NULL,
	"active" integer DEFAULT 1 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX pactive_index1 ON "public"."partners"("active");

CREATE TABLE "public"."pci_billing_histories" (
	"id" serial NOT NULL,
	"pci_billing_type_id" integer NOT NULL,
	"saq_merchant_id" integer NOT NULL,
	"billing_date" date DEFAULT NULL,
	"date_change" date NOT NULL,
	"operation" varchar(25) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."pci_billing_types" (
	"id" serial NOT NULL,
	"name" varchar(255) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."pci_billings" (
	"id" serial NOT NULL,
	"pci_billing_type_id" integer NOT NULL,
	"saq_merchant_id" integer NOT NULL,
	"billing_date" date DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."pci_compliance_date_types" (
	"id" serial NOT NULL,
	"name" varchar(255) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."pci_compliance_status_logs" (
	"id" serial NOT NULL,
	"pci_compliance_date_type_id" integer DEFAULT NULL,
	"saq_merchant_id" integer DEFAULT NULL,
	"date_complete" date DEFAULT NULL,
	"date_change" timestamp ,
	"operation" varchar(25) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."pci_compliances" (
	"id" serial NOT NULL,
	"pci_compliance_date_type_id" integer NOT NULL,
	"saq_merchant_id" integer NOT NULL,
	"date_complete" date NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."pci_reminders_mv" (
	"id" serial NOT NULL,
	"merchant_id" varchar(50) DEFAULT NULL,
	"saq_deadline" timestamp ,
	"scan_deadline" timestamp ,
	"saq_status" text DEFAULT NULL,
	"scan_status" text DEFAULT NULL,
	"saq_date_type_id" integer DEFAULT NULL,
	"scan_date_type_id" integer DEFAULT NULL
);
CREATE INDEX pci_reminders_mv_merchant_id_idx ON "public"."pci_reminders_mv"("merchant_id");

CREATE TABLE "public"."permission_groups" (
	"id" serial NOT NULL,
	"permission_group" varchar(20) NOT NULL,
	"permission_group_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."pricing_matrixes" (
	"id" serial NOT NULL,
	"matrix_id" integer NOT NULL,
	"user_id" integer NOT NULL,
	"user_type" varchar(10) NOT NULL,
	"matrix_profit_perc" float DEFAULT NULL,
	"matrix_new_monthly_volume" float DEFAULT NULL,
	"matrix_new_monthly_profit" float DEFAULT NULL,
	"matrix_view_conjunction" varchar(10) DEFAULT NULL,
	"matrix_total_volume" float DEFAULT NULL,
	"matrix_total_accounts" float DEFAULT NULL,
	"matrix_total_profit" float DEFAULT NULL,
	"matrix_draw" float DEFAULT NULL,
	"matrix_new_accounts_min" float DEFAULT NULL,
	"matrix_new_accounts_max" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX pricing_matrix_user_idx ON "public"."pricing_matrixes"("user_id");
CREATE INDEX pricing_matrix_usertype ON "public"."pricing_matrixes"("user_type");

CREATE TABLE "public"."products_and_services" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"products_services_type" varchar(10) NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX p_and_s_merchant_idx ON "public"."products_and_services"("merchant_id");
CREATE INDEX products_and_services_merchant_id_idx ON "public"."products_and_services"("merchant_id");

CREATE TABLE "public"."products_services_types" (
	"id" serial NOT NULL,
	"products_services_type" varchar(10) NOT NULL,
	"products_services_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."referer_products_services_xrefs" (
	"id" serial NOT NULL,
	"ref_seq_number" integer NOT NULL,
	"products_services_type" varchar(10) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."referers" (
	"id" serial NOT NULL,
	"ref_seq_number" integer NOT NULL,
	"ref_name" varchar(50) DEFAULT NULL,
	"ref_ref_perc" float DEFAULT NULL,
	"active" integer DEFAULT NULL,
	"ref_username" varchar(50) DEFAULT NULL,
	"ref_password" varchar(50) DEFAULT NULL,
	"ref_residuals" boolean NOT NULL,
	"ref_commissions" boolean NOT NULL,
	"is_referer" boolean DEFAULT 'TRUE' NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX referers_active ON "public"."referers"("active");

CREATE TABLE "public"."referers_bets" (
	"id" serial NOT NULL,
	"referers_bet_id" integer NOT NULL,
	"ref_seq_number" integer NOT NULL,
	"bet_code" varchar(15) NOT NULL,
	"pct" float DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX referers_bet_index1 ON "public"."referers_bets"("ref_seq_number");
CREATE INDEX referers_bet_index2 ON "public"."referers_bets"("bet_code");

CREATE TABLE "public"."rep_cost_structures" (
	"id" serial NOT NULL,
	"user_id" integer NOT NULL,
	"debit_monthly_fee" float DEFAULT NULL,
	"debit_per_item_fee" float DEFAULT NULL,
	"gift_statement_fee" float DEFAULT NULL,
	"gift_magstripe_item_fee" float DEFAULT NULL,
	"gift_magstripe_loyalty_fee" float DEFAULT NULL,
	"gift_chipcard_item_fee" float DEFAULT NULL,
	"gift_chipcard_loyalty_fee" float DEFAULT NULL,
	"gift_chipcard_onerate_fee" float DEFAULT NULL,
	"cg_volume" float DEFAULT NULL,
	"vc_web_based_rate" float DEFAULT NULL,
	"vc_web_based_pi" float DEFAULT NULL,
	"vc_monthly_fee" float DEFAULT NULL,
	"vc_gateway_fee" float DEFAULT NULL,
	"ach_merchant_based" float DEFAULT NULL,
	"ach_file_fee" float DEFAULT NULL,
	"ach_eft_ccd_nw" float DEFAULT NULL,
	"ach_eft_ccd_w" float DEFAULT NULL,
	"ach_eft_ppd_nw" float DEFAULT NULL,
	"ach_eft_ppd_w" float DEFAULT NULL,
	"ach_eft_rck" float DEFAULT NULL,
	"ach_reject_fee" float DEFAULT NULL,
	"ach_statement_fee" float DEFAULT NULL,
	"ach_secure_gateway_eft" float DEFAULT NULL,
	"ebt_per_item_fee" float DEFAULT NULL,
	"ebt_monthly_fee" float DEFAULT NULL,
	"main_statement_fee" float DEFAULT NULL,
	"main_vrt_term_gwy_fee" float DEFAULT NULL,
	"vc_web_based_monthly_fee" float DEFAULT NULL,
	"vc_web_based_gateway_fee" float DEFAULT NULL,
	"te_transaction_fee" float DEFAULT NULL,
	"af_annual_fee" float DEFAULT NULL,
	"multiple" float DEFAULT NULL,
	"auth_monthly_fee" float DEFAULT NULL,
	"auth_per_item_fee" float DEFAULT NULL,
	"debit_rate_rep_cost_pct" float DEFAULT NULL,
	"ach_rate" float DEFAULT NULL,
	"gw_rate" float DEFAULT NULL,
	"gw_per_item" float DEFAULT NULL,
	"debit_per_item_fee_tsys" float DEFAULT NULL,
	"debit_rate_rep_cost_pct_tsys" float DEFAULT NULL,
	"debit_per_item_fee_tsys_new" float DEFAULT NULL,
	"debit_rate_rep_cost_pct_tsys_new" float DEFAULT NULL,
	"discover_statement_fee" float DEFAULT NULL,
	"debit_per_item_fee_directconnect" float DEFAULT NULL,
	"debit_rate_rep_cost_pct_directconnect" float DEFAULT NULL,
	"debit_per_item_fee_sagepti" float DEFAULT NULL,
	"debit_rate_rep_cost_pct_sagepti" float DEFAULT NULL,
	"tg_rate" float DEFAULT NULL,
	"tg_per_item" float DEFAULT NULL,
	"wp_rate" float DEFAULT NULL,
	"wp_per_item" float DEFAULT NULL,
	"tg_statement_fee" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."rep_partner_xrefs" (
	"id" serial NOT NULL,
	"user_id" integer NOT NULL,
	"partner_id" integer NOT NULL,
	"mgr1_id" integer DEFAULT NULL,
	"mgr2_id" integer DEFAULT NULL,
	"profit_pct" float DEFAULT NULL,
	"multiple" integer DEFAULT NULL,
	"mgr1_profit_pct" float DEFAULT NULL,
	"mgr2_profit_pct" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX rpxref_index1 ON "public"."rep_partner_xrefs"("mgr1_id");
CREATE INDEX rpxref_index2 ON "public"."rep_partner_xrefs"("mgr2_id");

CREATE TABLE "public"."residual_pricings" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"products_services_type" varchar(10) NOT NULL,
	"user_id" integer NOT NULL,
	"r_network" varchar(50) DEFAULT NULL,
	"r_month" float DEFAULT NULL,
	"r_year" float DEFAULT NULL,
	"r_rate_pct" float DEFAULT NULL,
	"r_per_item_fee" float DEFAULT NULL,
	"r_statement_fee" float DEFAULT NULL,
	"m_rate_pct" float DEFAULT NULL,
	"m_per_item_fee" float DEFAULT NULL,
	"m_statement_fee" float DEFAULT NULL,
	"refer_profit_pct" float DEFAULT NULL,
	"ref_seq_number" integer DEFAULT NULL,
	"m_hidden_per_item_fee" float DEFAULT NULL,
	"m_hidden_per_item_fee_cross" float DEFAULT NULL,
	"m_eft_secure_flag" integer DEFAULT NULL,
	"m_gc_plan" varchar(8) DEFAULT NULL,
	"m_pos_partner_flag" integer DEFAULT NULL,
	"bet_code" varchar(15) DEFAULT NULL,
	"bet_extra_pct" float DEFAULT NULL,
	"pct_volume" boolean DEFAULT NULL,
	"res_profit_pct" float DEFAULT NULL,
	"res_seq_number" integer DEFAULT NULL,
	"m_micros_ip_flag" integer DEFAULT NULL,
	"m_micros_dialup_flag" integer DEFAULT NULL,
	"m_micros_per_item_fee" float DEFAULT NULL,
	"m_wireless_flag" integer DEFAULT NULL,
	"m_wireless_terminals" integer DEFAULT NULL,
	"r_usaepay_flag" integer DEFAULT NULL,
	"r_epay_retail_flag" integer DEFAULT NULL,
	"r_usaepay_gtwy_cost" float DEFAULT NULL,
	"r_usaepay_gtwy_add_cost" float DEFAULT NULL,
	"m_tgate_flag" integer DEFAULT NULL,
	"m_petro_flag" integer DEFAULT NULL,
	"ref_p_type" text DEFAULT NULL,
	"ref_p_value" float DEFAULT NULL,
	"res_p_type" text DEFAULT NULL,
	"res_p_value" float DEFAULT NULL,
	"r_risk_assessment" float DEFAULT NULL,
	"ref_p_pct" integer DEFAULT NULL,
	"res_p_pct" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX residual_pricing_mid ON "public"."residual_pricings"("merchant_id");
CREATE INDEX residual_pricing_month ON "public"."residual_pricings"("r_month");
CREATE INDEX residual_pricing_network ON "public"."residual_pricings"("r_network");
CREATE INDEX residual_pricing_pst ON "public"."residual_pricings"("products_services_type");
CREATE INDEX residual_pricing_userid ON "public"."residual_pricings"("user_id");
CREATE INDEX residual_pricing_year ON "public"."residual_pricings"("r_year");
CREATE INDEX residual_pricings_merchant_id_idx ON "public"."residual_pricings"("merchant_id");

CREATE TABLE "public"."residual_reports" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"products_services_type" varchar(10) NOT NULL,
	"user_id" integer NOT NULL,
	"r_network" varchar(50) DEFAULT NULL,
	"r_month" float DEFAULT NULL,
	"r_year" float DEFAULT NULL,
	"r_rate_pct" float DEFAULT NULL,
	"r_per_item_fee" float DEFAULT NULL,
	"r_statement_fee" float DEFAULT NULL,
	"r_profit_pct" float DEFAULT NULL,
	"r_profit_amount" float DEFAULT NULL,
	"m_rate_pct" float DEFAULT NULL,
	"m_per_item_fee" float DEFAULT NULL,
	"refer_profit_pct" float DEFAULT NULL,
	"refer_profit_amount" float DEFAULT NULL,
	"ref_seq_number" integer DEFAULT NULL,
	"status" varchar(5) DEFAULT NULL,
	"m_statement_fee" float DEFAULT NULL,
	"r_avg_ticket" float DEFAULT NULL,
	"r_items" float DEFAULT NULL,
	"r_volume" float DEFAULT NULL,
	"total_profit" float DEFAULT NULL,
	"manager_id" integer DEFAULT NULL,
	"manager_profit_pct" float DEFAULT NULL,
	"manager_profit_amount" float DEFAULT NULL,
	"bet_code" varchar(15) DEFAULT NULL,
	"bet_extra_pct" float DEFAULT NULL,
	"res_profit_pct" float DEFAULT NULL,
	"res_profit_amount" float DEFAULT NULL,
	"res_seq_number" integer DEFAULT NULL,
	"manager_id_secondary" integer DEFAULT NULL,
	"manager_profit_pct_secondary" float DEFAULT NULL,
	"manager_profit_amount_secondary" float DEFAULT NULL,
	"ref_p_type" text DEFAULT NULL,
	"ref_p_value" float DEFAULT NULL,
	"res_p_type" text DEFAULT NULL,
	"res_p_value" float DEFAULT NULL,
	"ref_p_pct" integer DEFAULT NULL,
	"res_p_pct" integer DEFAULT NULL,
	"partner_id" integer DEFAULT NULL,
	"partner_exclude_volume" boolean DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX residual_report_merchantid ON "public"."residual_reports"("merchant_id");
CREATE INDEX residual_report_month ON "public"."residual_reports"("r_month");
CREATE INDEX residual_report_pst ON "public"."residual_reports"("products_services_type");
CREATE INDEX residual_report_seqnum ON "public"."residual_reports"("ref_seq_number");
CREATE INDEX residual_report_status ON "public"."residual_reports"("status");
CREATE INDEX residual_report_userid ON "public"."residual_reports"("user_id");
CREATE INDEX residual_report_year ON "public"."residual_reports"("r_year");
CREATE INDEX residual_reports_merchant_id_idx ON "public"."residual_reports"("merchant_id");
CREATE INDEX rr_manager_id ON "public"."residual_reports"("manager_id");
CREATE INDEX rr_manager_id_secondary ON "public"."residual_reports"("manager_id_secondary");
CREATE INDEX rr_partner_id_index ON "public"."residual_reports"("partner_id");

CREATE TABLE "public"."sales_goal_archives" (
	"id" serial NOT NULL,
	"user_id" integer NOT NULL,
	"goal_month" integer DEFAULT NULL,
	"goal_year" integer DEFAULT NULL,
	"goal_accounts" integer DEFAULT NULL,
	"goal_volume" float DEFAULT NULL,
	"goal_profits" float DEFAULT NULL,
	"actual_accounts" float DEFAULT NULL,
	"actual_volume" float DEFAULT NULL,
	"actual_profits" float DEFAULT NULL,
	"goal_statements" float DEFAULT NULL,
	"goal_calls" float DEFAULT NULL,
	"actual_statements" float DEFAULT NULL,
	"actual_calls" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX sg_archive_user_idx ON "public"."sales_goal_archives"("user_id", "goal_month", "goal_year");

CREATE TABLE "public"."sales_goals" (
	"id" serial NOT NULL,
	"user_id" integer NOT NULL,
	"goal_accounts" float DEFAULT NULL,
	"goal_volume" float DEFAULT NULL,
	"goal_profits" float DEFAULT NULL,
	"goal_statements" float DEFAULT NULL,
	"goal_calls" float DEFAULT NULL,
	"goal_month" integer DEFAULT 1 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX sales_goal_userid ON "public"."sales_goals"("user_id");

CREATE TABLE "public"."saq_answers" (
	"id" serial NOT NULL,
	"saq_merchant_survey_xref_id" integer NOT NULL,
	"saq_survey_question_xref_id" integer NOT NULL,
	"answer" boolean NOT NULL,
	"date" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX sa_saq_merchant_survey_xref_id ON "public"."saq_answers"("saq_merchant_survey_xref_id");
CREATE INDEX sa_saq_survey_question_xref_id ON "public"."saq_answers"("saq_survey_question_xref_id");

CREATE TABLE "public"."saq_control_scan_unboardeds" (
	"id" serial NOT NULL,
	"merchant_id" integer DEFAULT NULL,
	"date_unboarded" date DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX saq_control_scan_unboardeds_merchant_id_idx ON "public"."saq_control_scan_unboardeds"("merchant_id");
CREATE INDEX scsu_date_unboarded ON "public"."saq_control_scan_unboardeds"("date_unboarded");

CREATE TABLE "public"."saq_control_scans" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"saq_type" varchar(4) DEFAULT NULL,
	"first_scan_date" date DEFAULT NULL,
	"first_questionnaire_date" date DEFAULT NULL,
	"scan_status" text DEFAULT NULL,
	"questionnaire_status" text DEFAULT NULL,
	"creation_date" date DEFAULT NULL,
	"dba" varchar(80) DEFAULT NULL,
	"quarterly_scan_fee" float DEFAULT '(11.949999999999999)' NOT NULL,
	"sua" timestamp ,
	"pci_compliance" varchar(3) DEFAULT NULL,
	"offline_compliance" varchar(3) DEFAULT NULL,
	"compliant_date" date DEFAULT NULL,
	"host_count" integer DEFAULT NULL,
	"submids" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX saq_control_scans_merchant_id_idx ON "public"."saq_control_scans"("merchant_id");
CREATE INDEX scs_creation_date ON "public"."saq_control_scans"("creation_date");
CREATE INDEX scs_first_questionnaire_date ON "public"."saq_control_scans"("first_questionnaire_date");
CREATE INDEX scs_first_scan_date ON "public"."saq_control_scans"("first_scan_date");
CREATE INDEX scs_pci_compliance ON "public"."saq_control_scans"("pci_compliance");
CREATE INDEX scs_quarterly_scan_fee ON "public"."saq_control_scans"("quarterly_scan_fee");
CREATE INDEX scs_questionnaire_status ON "public"."saq_control_scans"("questionnaire_status");
CREATE INDEX scs_saq_type ON "public"."saq_control_scans"("saq_type");
CREATE INDEX scs_scan_status ON "public"."saq_control_scans"("scan_status");
CREATE INDEX scs_sua ON "public"."saq_control_scans"("sua");

CREATE TABLE "public"."saq_eligible_mv" (
	"id" serial NOT NULL,
	"merchant_id" varchar(50) DEFAULT NULL,
	"merchant_email" varchar(100) DEFAULT NULL,
	"merchant_name" varchar(255) DEFAULT NULL,
	"password" varchar(255) DEFAULT NULL,
	"email_sent" timestamp ,
	"saq_type" varchar(4) DEFAULT NULL,
	"first_scan_date" date DEFAULT NULL,
	"first_questionnaire_date" date DEFAULT NULL,
	"scan_status" text DEFAULT NULL,
	"questionnaire_status" text DEFAULT NULL,
	"pci_compliance" varchar(3) DEFAULT NULL,
	"creation_date" date DEFAULT NULL,
	"sua" timestamp ,
	"saq_completed_date" date DEFAULT NULL,
	"billing_date" timestamp ,
	"next_billing_date" timestamp ,
	"quarterly_scan_fee" float DEFAULT NULL,
	"compliance_fee" float DEFAULT NULL,
	"insurance_fee" float DEFAULT NULL
);
CREATE INDEX saq_eligible_mv_merchant_id_idx ON "public"."saq_eligible_mv"("merchant_id");

CREATE TABLE "public"."saq_merchant_pci_email_sents" (
	"id" serial NOT NULL,
	"saq_merchant_id" integer NOT NULL,
	"saq_merchant_pci_email_id" integer NOT NULL,
	"date_sent" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX smpes_date_sent ON "public"."saq_merchant_pci_email_sents"("date_sent");
CREATE INDEX smpes_saq_merchant_id ON "public"."saq_merchant_pci_email_sents"("saq_merchant_id");
CREATE INDEX smpes_saq_merchant_pci_email_id ON "public"."saq_merchant_pci_email_sents"("saq_merchant_pci_email_id");

CREATE TABLE "public"."saq_merchant_pci_emails" (
	"id" serial NOT NULL,
	"priority" integer NOT NULL,
	"interval" integer NOT NULL,
	"title" varchar(255) NOT NULL,
	"filename_prefix" varchar(255) NOT NULL,
	"visible" boolean DEFAULT 'TRUE' NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."saq_merchant_survey_xrefs" (
	"id" serial NOT NULL,
	"saq_merchant_id" integer NOT NULL,
	"saq_survey_id" integer NOT NULL,
	"saq_eligibility_survey_id" integer DEFAULT NULL,
	"saq_confirmation_survey_id" integer DEFAULT NULL,
	"datestart" timestamp NOT NULL,
	"datecomplete" timestamp ,
	"ip" varchar(15) DEFAULT NULL,
	"acknowledgement_name" varchar(255) DEFAULT NULL,
	"acknowledgement_title" varchar(255) DEFAULT NULL,
	"acknowledgement_company" varchar(255) DEFAULT NULL,
	"resolution" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX smsx_acknowledgement_name ON "public"."saq_merchant_survey_xrefs"("acknowledgement_name");
CREATE INDEX smsx_datecomplete ON "public"."saq_merchant_survey_xrefs"("datecomplete");
CREATE INDEX smsx_saq_confirmation_survey_id ON "public"."saq_merchant_survey_xrefs"("saq_confirmation_survey_id");
CREATE INDEX smsx_saq_eligibility_survey_id ON "public"."saq_merchant_survey_xrefs"("saq_eligibility_survey_id");
CREATE INDEX smsx_saq_merchant_id ON "public"."saq_merchant_survey_xrefs"("saq_merchant_id");
CREATE INDEX smsx_saq_survey_id ON "public"."saq_merchant_survey_xrefs"("saq_survey_id");

CREATE TABLE "public"."saq_merchants" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"merchant_name" varchar(255) NOT NULL,
	"merchant_email" varchar(100) NOT NULL,
	"password" varchar(255) NOT NULL,
	"email_sent" timestamp ,
	"billing_date" timestamp ,
	"next_billing_date" timestamp ,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX saq_merchants_merchant_id_idx ON "public"."saq_merchants"("merchant_id");
CREATE UNIQUE INDEX saw_merchant_idx ON "public"."saq_merchants"("merchant_id");
CREATE INDEX sm_billing_date ON "public"."saq_merchants"("billing_date");
CREATE INDEX sm_email_sent ON "public"."saq_merchants"("email_sent");
CREATE INDEX sm_merchant_email ON "public"."saq_merchants"("merchant_email");
CREATE INDEX sm_merchant_name ON "public"."saq_merchants"("merchant_name");
CREATE INDEX sm_next_billing_date ON "public"."saq_merchants"("next_billing_date");
CREATE INDEX sm_password ON "public"."saq_merchants"("password");

CREATE TABLE "public"."saq_prequalifications" (
	"id" serial NOT NULL,
	"saq_merchant_id" integer NOT NULL,
	"result" varchar(4) NOT NULL,
	"date_completed" timestamp NOT NULL,
	"control_scan_code" integer DEFAULT NULL,
	"control_scan_message" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX sp_saq_merchant_id ON "public"."saq_prequalifications"("saq_merchant_id");

CREATE TABLE "public"."saq_questions" (
	"id" serial NOT NULL,
	"question" text NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."saq_survey_question_xrefs" (
	"id" serial NOT NULL,
	"saq_survey_id" integer NOT NULL,
	"saq_question_id" integer NOT NULL,
	"priority" integer NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX ssqx_saq_question_id ON "public"."saq_survey_question_xrefs"("saq_question_id");
CREATE INDEX ssqx_saq_survey_id ON "public"."saq_survey_question_xrefs"("saq_survey_id");

CREATE TABLE "public"."saq_surveys" (
	"id" serial NOT NULL,
	"name" varchar(255) NOT NULL,
	"saq_level" varchar(4) DEFAULT NULL,
	"eligibility_survey_id" integer DEFAULT NULL,
	"confirmation_survey_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX ss_confirmation_survey_id ON "public"."saq_surveys"("confirmation_survey_id");
CREATE INDEX ss_eligibility_survey_id ON "public"."saq_surveys"("eligibility_survey_id");

CREATE TABLE "public"."schema_migrations" (
	"id" serial NOT NULL,
	"class" varchar(255) NOT NULL,
	"type" varchar(50) NOT NULL,
	"created" timestamp NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."shipping_type_items" (
	"id" serial NOT NULL,
	"shipping_type" integer NOT NULL,
	"shipping_type_description" varchar(50) NOT NULL,
	"cost" float NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."shipping_types" (
	"id" serial NOT NULL,
	"shipping_type" integer NOT NULL,
	"shipping_type_description" varchar(50) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."system_transactions" (
	"id" serial NOT NULL,
	"system_transaction_id" integer NOT NULL,
	"transaction_type" varchar(10) NOT NULL,
	"user_id" integer NOT NULL,
	"merchant_id" integer DEFAULT NULL,
	"session_id" varchar(50) DEFAULT NULL,
	"client_address" varchar(100) DEFAULT NULL,
	"system_transaction_date" date DEFAULT NULL,
	"system_transaction_time" time DEFAULT NULL,
	"merchant_note_id" integer DEFAULT NULL,
	"change_id" integer DEFAULT NULL,
	"ach_seq_number" integer DEFAULT NULL,
	"order_id" integer DEFAULT NULL,
	"programming_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX sys_trans_merchant_idx ON "public"."system_transactions"("merchant_id");
CREATE INDEX sys_trans_session_idx ON "public"."system_transactions"("session_id");
CREATE INDEX system_transaction_chnageid ON "public"."system_transactions"("change_id");
CREATE INDEX system_transaction_noteid ON "public"."system_transactions"("merchant_note_id");
CREATE INDEX system_transaction_orderid ON "public"."system_transactions"("order_id");
CREATE INDEX system_transaction_programmingi ON "public"."system_transactions"("programming_id");
CREATE INDEX system_transaction_userid ON "public"."system_transactions"("user_id");
CREATE INDEX system_transactions_merchant_id_idx ON "public"."system_transactions"("merchant_id");

CREATE TABLE "public"."tgates" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"tg_rate" float DEFAULT NULL,
	"tg_per_item" float DEFAULT NULL,
	"tg_statement" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX tgates_merchant_id_idx ON "public"."tgates"("merchant_id");

CREATE TABLE "public"."timeline_entries" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"timeline_item" varchar(50) NOT NULL,
	"timeline_date_completed" date DEFAULT NULL,
	"action_flag" boolean DEFAULT NULL,
	"timeline_item_id" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX timeline_entries_merchant_id_idx ON "public"."timeline_entries"("merchant_id");
CREATE INDEX tl_ent_merchant_idx ON "public"."timeline_entries"("merchant_id");
CREATE INDEX tl_timeline_date_completed ON "public"."timeline_entries"("timeline_date_completed");
CREATE INDEX tl_timeline_item ON "public"."timeline_entries"("timeline_item");

CREATE TABLE "public"."timeline_items" (
	"id" serial NOT NULL,
	"timeline_item" varchar(50) NOT NULL,
	"timeline_item_description" varchar(100) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."transaction_types" (
	"id" serial NOT NULL,
	"transaction_type" varchar(10) NOT NULL,
	"transaction_type_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."usaepay_rep_gtwy_add_costs" (
	"id" serial NOT NULL,
	"name" varchar(255) NOT NULL,
	"cost" float NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."usaepay_rep_gtwy_costs" (
	"id" serial NOT NULL,
	"name" varchar(255) NOT NULL,
	"cost" float NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."user_bet_tables" (
	"id" serial NOT NULL,
	"bet_code" varchar(15) NOT NULL,
	"user_id" integer NOT NULL,
	"network" varchar(20) NOT NULL,
	"rate" float DEFAULT NULL,
	"pi" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX user_bet_table_pk ON "public"."user_bet_tables"("bet_code", "user_id", "network");
CREATE INDEX ubt_user_idx ON "public"."user_bet_tables"("user_id");

CREATE TABLE "public"."user_permissions" (
	"id" serial NOT NULL,
	"user_id" integer NOT NULL,
	"permission_id" integer NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX user_permission_user_idx ON "public"."user_permissions"("user_id");

CREATE TABLE "public"."user_types" (
	"id" serial NOT NULL,
	"user_type" varchar(10) NOT NULL,
	"user_type_description" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."users" (
	"id" serial NOT NULL,
	"user_did" integer DEFAULT NULL,
	"user_type_id" integer NOT NULL,
	"user_title" varchar(20) DEFAULT NULL,
	"user_first_name" varchar(50) DEFAULT NULL,
	"user_last_name" varchar(50) DEFAULT NULL,
	"username" varchar(50) DEFAULT NULL,
	"password_2" varchar(50) DEFAULT NULL,
	"user_email" varchar(50) DEFAULT NULL,
	"user_phone" varchar(20) DEFAULT NULL,
	"user_fax" varchar(20) DEFAULT NULL,
	"user_admin" varchar(1) DEFAULT NULL,
	"parent_user" integer DEFAULT NULL,
	"user_commission" integer DEFAULT NULL,
	"inactive_date" date DEFAULT NULL,
	"last_login_date" date DEFAULT NULL,
	"last_login_ip" varchar(12) DEFAULT NULL,
	"entity" varchar(10) DEFAULT NULL,
	"active" integer DEFAULT NULL,
	"initials" varchar(5) DEFAULT NULL,
	"manager_percentage" float DEFAULT NULL,
	"date_started" date DEFAULT NULL,
	"split_commissions" boolean NOT NULL,
	"bet_extra_pct" boolean NOT NULL,
	"parent_user_secondary" integer DEFAULT NULL,
	"manager_percentage_secondary" float DEFAULT NULL,
	"discover_bet_extra_pct" boolean NOT NULL,
	"password" varchar(50) DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX user_email_idx ON "public"."users"("user_email");
CREATE INDEX user_lastname_idx ON "public"."users"("user_last_name");
CREATE INDEX user_parent_user_idx ON "public"."users"("parent_user");
CREATE INDEX user_parent_user_secondary_idx ON "public"."users"("parent_user_secondary");
CREATE INDEX user_username_idx ON "public"."users"("username");

CREATE TABLE "public"."uw_approvalinfo_merchant_xrefs" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"approvalinfo_id" integer NOT NULL,
	"verified_option_id" integer NOT NULL,
	"notes" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX uw_amx_verified_option_id_index ON "public"."uw_approvalinfo_merchant_xrefs"("verified_option_id");
CREATE INDEX uw_approvalinfo_merchant_xrefs_merchant_id_idx ON "public"."uw_approvalinfo_merchant_xrefs"("merchant_id");

CREATE TABLE "public"."uw_approvalinfos" (
	"id" serial NOT NULL,
	"name" varchar(99) NOT NULL,
	"priority" integer DEFAULT 0 NOT NULL,
	"verified_type" text NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX uw_approvalinfo_priority_index ON "public"."uw_approvalinfos"("priority");
CREATE INDEX uw_approvalinfo_verified_type_index ON "public"."uw_approvalinfos"("verified_type");

CREATE TABLE "public"."uw_infodoc_merchant_xrefs" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"infodoc_id" integer NOT NULL,
	"received_id" integer NOT NULL,
	"notes" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX uw_imx_received_id_index ON "public"."uw_infodoc_merchant_xrefs"("received_id");
CREATE INDEX uw_infodoc_merchant_xrefs_merchant_id_idx ON "public"."uw_infodoc_merchant_xrefs"("merchant_id");

CREATE TABLE "public"."uw_infodocs" (
	"id" serial NOT NULL,
	"name" varchar(99) NOT NULL,
	"priority" integer DEFAULT 0 NOT NULL,
	"required" boolean NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX uw_infodoc_priority_index ON "public"."uw_infodocs"("priority");
CREATE INDEX uw_infodoc_required_index ON "public"."uw_infodocs"("required");

CREATE TABLE "public"."uw_receiveds" (
	"id" serial NOT NULL,
	"name" varchar(11) NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."uw_status_merchant_xrefs" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"status_id" integer NOT NULL,
	"datetime" timestamp NOT NULL,
	"notes" text DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX uw_smx_datetime_index ON "public"."uw_status_merchant_xrefs"("datetime");
CREATE INDEX uw_status_merchant_xrefs_merchant_id_idx ON "public"."uw_status_merchant_xrefs"("merchant_id");

CREATE TABLE "public"."uw_statuses" (
	"id" serial NOT NULL,
	"name" varchar(99) NOT NULL,
	"priority" integer DEFAULT 0 NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX uw_status_priority_index ON "public"."uw_statuses"("priority");

CREATE TABLE "public"."uw_verified_options" (
	"id" serial NOT NULL,
	"name" varchar(37) NOT NULL,
	"verified_type" text NOT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX uw_verified_option_verified_type_index ON "public"."uw_verified_options"("verified_type");

CREATE TABLE "public"."vanilla_acos" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT NULL,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT NULL,
	"alias" varchar(255) DEFAULT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."vanilla_aros" (
	"id" serial NOT NULL,
	"parent_id" integer DEFAULT NULL,
	"model" varchar(255) DEFAULT NULL,
	"foreign_key" integer DEFAULT NULL,
	"alias" varchar(255) DEFAULT NULL,
	"lft" integer DEFAULT NULL,
	"rght" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."vanilla_aros_acos" (
	"id" serial NOT NULL,
	"aro_id" integer NOT NULL,
	"aco_id" integer NOT NULL,
	"_create" varchar(2) DEFAULT '0' NOT NULL,
	"_read" varchar(2) DEFAULT '0' NOT NULL,
	"_update" varchar(2) DEFAULT '0' NOT NULL,
	"_delete" varchar(2) DEFAULT '0' NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."vanilla_posts" (
	"id" serial NOT NULL,
	"user_id" integer NOT NULL,
	"title" varchar(255) NOT NULL,
	"body" text DEFAULT NULL,
	"created" timestamp ,
	"modified" timestamp ,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."vanilla_roles" (
	"id" serial NOT NULL,
	"name" varchar(100) NOT NULL,
	"created" timestamp ,
	"modified" timestamp ,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."vanilla_users" (
	"id" serial NOT NULL,
	"username" varchar(255) NOT NULL,
	"password" varchar(40) NOT NULL,
	"role_id" integer NOT NULL,
	"created" timestamp ,
	"modified" timestamp ,
	PRIMARY KEY  ("id")
);
CREATE UNIQUE INDEX vanilla_users_username_key ON "public"."vanilla_users"("username");

CREATE TABLE "public"."vanilla_widgets" (
	"id" serial NOT NULL,
	"name" varchar(100) NOT NULL,
	"part_no" varchar(12) DEFAULT NULL,
	"quantity" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."vendors" (
	"id" serial NOT NULL,
	"vendor_id" integer NOT NULL,
	"vendor_description" varchar(50) NOT NULL,
	"rank" integer DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX vendor_rank ON "public"."vendors"("rank");

CREATE TABLE "public"."virtual_check_webs" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"vcweb_mid" varchar(20) DEFAULT NULL,
	"vcweb_web_based_rate" float DEFAULT NULL,
	"vcweb_web_based_pi" float DEFAULT NULL,
	"vcweb_monthly_fee" float DEFAULT NULL,
	"vcweb_gateway_fee" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX virtual_check_webs_merchant_id_idx ON "public"."virtual_check_webs"("merchant_id");

CREATE TABLE "public"."virtual_checks" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"vc_mid" varchar(20) DEFAULT NULL,
	"vc_web_based_rate" float DEFAULT NULL,
	"vc_web_based_pi" float DEFAULT NULL,
	"vc_monthly_fee" float DEFAULT NULL,
	"vc_gateway_fee" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX virtual_check_mid ON "public"."virtual_checks"("vc_mid");
CREATE INDEX virtual_checks_merchant_id_idx ON "public"."virtual_checks"("merchant_id");

CREATE TABLE "public"."warranties" (
	"id" serial NOT NULL,
	"warranty" integer NOT NULL,
	"warranty_description" varchar(50) NOT NULL,
	"cost" float NOT NULL,
	PRIMARY KEY  ("id")
);


CREATE TABLE "public"."webpasses" (
	"id" serial NOT NULL,
	"merchant_id" integer NOT NULL,
	"wp_rate" float DEFAULT NULL,
	"wp_per_item" float DEFAULT NULL,
	"wp_statement" float DEFAULT NULL,
	PRIMARY KEY  ("id")
);
CREATE INDEX webpasses_merchant_id_idx ON "public"."webpasses"("merchant_id");

