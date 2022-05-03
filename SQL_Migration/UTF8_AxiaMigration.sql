--# Logged in to the 8.4 database as postgres
--sudo -u postgres -i
--#do pg_dump of legacy axia database
--/usr/lib/postgresql/8.4/bin/pg_dump axia -h localhost -E 'WIN1250' -U axia > LegacyAxiaDB.sql
--#start psql admin
--psql
--Create new database using DB template0, which is UTF8.
--CREATE DATABASE axiacp TEMPLATE template0 LC_COLLATE = 'C' LC_CTYPE = 'C';
--GRANT ALL ON DATABASE axiacp TO axia
--\q
--#logout as progress
--exit
--#pg_restore into the new empty database
--/usr/lib/postgresql/8.4/bin/psql -d axiacp -f LegacyAxiaDB.sql

--#NOTE: pg dump and restore will automatically convert from the encoding specified with -E in the pg_dump command 
--#to the encoding set on the database being dumped into.
--#Some data encoding does not have equivalent UTF8 conversion. Tables containing that data will not be populated.
--#To find and populate those tables continue with the steps below
--###############################

--#Assuming a new empty database has been created on the 9.2 database, run the schema and migration scripts.
--#We want UTF8 data so make sure migration script connects to axiacp not axia using select dblink_connect_u('pg84', 'dbname=axiacp');
--psql -h localhost -U axia -p 5433 < updatedUUIDsAxiaSchema\(CAKE\)2.sql
--psql -h localhost -U axia -p 5433 < SQL\ MIGRATION.sql > ~/migrateTst.tst 2>&1 

--#connect to 9.2 axia database
--psql -h localhost -U axia -p 5433

--Run function to find tables that did not get pupulated due to lack of UTF8 encoding equivalence
--SELECT findEmptyTables()
--Copy/Pase below the insert, alter and/or delete statements that correspond to each empty table from the main migration 
--script and run it but this time connect to the axia database NOT axiacp.
-- DROP axiacp database
\timing
select dblink_connect_u('pg84', 'dbname=axia_legacy');

------------------------------MIGRATION CODE FOR EMPTY TABLES----------------------------



/*
INSERT INTO merchant_changes(change_id,change_type_id,merchant_id,user_id,status,date_entered,time_entered,date_approved,time_approved,change_data,merchant_note_id,approved_by_user_id,programming_id)
SELECT * FROM dblink('pg84', 'SELECT * FROM merchant_change')
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
Delete from merchant_changes  where user_id='0';
DELETE FROM merchant_changes where merchant_id LIKE 'TEST%';


INSERT INTO merchant_notes (merchant_note_id,note_type_id,user_id,merchant_id,note_date,note,note_title,general_status,date_changed,critical,note_sent)
SELECT * FROM dblink('pg84', 'SELECT * from merchant_note ORDER BY merchant_note_id ASC')
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


DELETE FROM merchant_notes WHERE merchant_id = 'BECKER';
DELETE FROM merchant_notes WHERE merchant_id LIKE '''%';
DELETE FROM merchant_notes where merchant_id LIKE 'TEST%';
DELETE FROM merchant_notes where merchant_id LIKE '%+%';
DELETE FROM merchant_notes where merchant_id LIKE '%-%';
DELETE FROM merchant_notes where merchant_id LIKE '%FAT%';
DELETE FROM merchant_notes where merchant_id = '';
DELETE FROM merchant_notes where char_length(merchant_id) > 16;
DELETE FROM merchant_notes where merchant_note_id in ('71501','71429','71314','70708','70706','70705','70702','46854','2782','2781','2780','20');


INSERT INTO saq_merchant_survey_xrefs (old_id,saq_merchant_id,saq_survey_id,saq_eligibility_survey_id,saq_confirmation_survey_id,datestart,datecomplete,ip,acknowledgement_name,acknowledgement_title,acknowledgement_company,resolution) 
select * from dblink('pg84', 'select * from saq_merchant_survey_xref')
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



INSERT INTO tickler_leads (id, business_name,business_address,city,state_id,zip,phone,mobile,fax,decision_maker,decision_maker_title,other_contact,other_contact_title,email,website,company_id,equipment_id,referer_id,reseller_id,user_id,status_id,likes1,likes2,likes3,dislikes1,dislikes2,dislikes3,created,modified,call_date,meeting_date,sign_date,app_created,nlp_id,volume,lat,lng ) 
select * from dblink('pg84', 'select * from tickler_leads')
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
*/




---------------------------------UPDATES----------------------------------------
BEGIN;


 DELETE FROM achs WHERE merchant_id in (SELECT achs.merchant_id FROM achs LEFT JOIN merchants ON achs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM addresses WHERE merchant_id in (SELECT addresses.merchant_id FROM addresses LEFT JOIN merchants ON addresses.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM amexes WHERE merchant_id in (SELECT amexes.merchant_id FROM amexes LEFT JOIN merchants ON amexes.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM authorizes WHERE merchant_id in (SELECT authorizes.merchant_id FROM authorizes LEFT JOIN merchants ON authorizes.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM bankcards WHERE merchant_id in (SELECT bankcards.merchant_id FROM bankcards LEFT JOIN merchants ON bankcards.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM check_guarantees WHERE merchant_id in (SELECT check_guarantees.merchant_id FROM check_guarantees LEFT JOIN merchants ON check_guarantees.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM commission_pricings WHERE merchant_id in (SELECT commission_pricings.merchant_id FROM commission_pricings LEFT JOIN merchants ON commission_pricings.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM commission_reports WHERE merchant_id in (SELECT commission_reports.merchant_id FROM commission_reports LEFT JOIN merchants ON commission_reports.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM debits WHERE merchant_id in (SELECT debits.merchant_id FROM debits LEFT JOIN merchants ON debits.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM discovers WHERE merchant_id in (SELECT discovers.merchant_id FROM discovers LEFT JOIN merchants ON discovers.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM ebts WHERE merchant_id in (SELECT ebts.merchant_id FROM ebts LEFT JOIN merchants ON ebts.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM equipment_programmings WHERE merchant_id in (SELECT equipment_programmings.merchant_id FROM equipment_programmings LEFT JOIN merchants ON equipment_programmings.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM gateway0s WHERE merchant_id in (SELECT gateway0s.merchant_id FROM gateway0s LEFT JOIN merchants ON gateway0s.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM gateway1s WHERE merchant_id in (SELECT gateway1s.merchant_id FROM gateway1s LEFT JOIN merchants ON gateway1s.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM gateway2s WHERE merchant_id in (SELECT gateway2s.merchant_id FROM gateway2s LEFT JOIN merchants ON gateway2s.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM gift_cards WHERE merchant_id in (SELECT gift_cards.merchant_id FROM gift_cards LEFT JOIN merchants ON gift_cards.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM last_deposit_reports WHERE merchant_id in (SELECT last_deposit_reports.merchant_id FROM last_deposit_reports LEFT JOIN merchants ON last_deposit_reports.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_achs WHERE merchant_id in (SELECT merchant_achs.merchant_id FROM merchant_achs LEFT JOIN merchants ON merchant_achs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_banks WHERE merchant_id in (SELECT merchant_banks.merchant_id FROM merchant_banks LEFT JOIN merchants ON merchant_banks.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_cancellations WHERE merchant_id in (SELECT merchant_cancellations.merchant_id FROM merchant_cancellations LEFT JOIN merchants ON merchant_cancellations.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_card_types WHERE merchant_id in (SELECT merchant_card_types.merchant_id FROM merchant_card_types LEFT JOIN merchants ON merchant_card_types.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_changes WHERE merchant_id in (SELECT merchant_changes.merchant_id FROM merchant_changes LEFT JOIN merchants ON merchant_changes.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_notes WHERE merchant_id in (SELECT merchant_notes.merchant_id FROM merchant_notes LEFT JOIN merchants ON merchant_notes.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_owners WHERE merchant_id in (SELECT merchant_owners.merchant_id FROM merchant_owners LEFT JOIN merchants ON merchant_owners.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_pcis WHERE merchant_id in (SELECT merchant_pcis.merchant_id FROM merchant_pcis LEFT JOIN merchants ON merchant_pcis.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_references WHERE merchant_id in (SELECT merchant_references.merchant_id FROM merchant_references LEFT JOIN merchants ON merchant_references.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_rejects WHERE merchant_id in (SELECT merchant_rejects.merchant_id FROM merchant_rejects LEFT JOIN merchants ON merchant_rejects.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_uws WHERE merchant_id in (SELECT merchant_uws.merchant_id FROM merchant_uws LEFT JOIN merchants ON merchant_uws.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM onlineapp_epayments WHERE merchant_id in (SELECT onlineapp_epayments.merchant_id FROM onlineapp_epayments LEFT JOIN merchants ON onlineapp_epayments.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM onlineapp_multipasses WHERE merchant_id in (SELECT onlineapp_multipasses.merchant_id FROM onlineapp_multipasses LEFT JOIN merchants ON onlineapp_multipasses.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM orders WHERE merchant_id in (SELECT orders.merchant_id FROM orders LEFT JOIN merchants ON orders.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM products_and_services WHERE merchant_id in (SELECT products_and_services.merchant_id FROM products_and_services LEFT JOIN merchants ON products_and_services.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM residual_pricings WHERE merchant_id in (SELECT residual_pricings.merchant_id FROM residual_pricings LEFT JOIN merchants ON residual_pricings.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM residual_reports WHERE merchant_id in (SELECT residual_reports.merchant_id FROM residual_reports LEFT JOIN merchants ON residual_reports.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM saq_control_scans WHERE merchant_id in (SELECT saq_control_scans.merchant_id FROM saq_control_scans LEFT JOIN merchants ON saq_control_scans.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM saq_control_scan_unboardeds WHERE merchant_id in (SELECT saq_control_scan_unboardeds.merchant_id FROM saq_control_scan_unboardeds LEFT JOIN merchants ON saq_control_scan_unboardeds.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM saq_merchants WHERE merchant_id in (SELECT saq_merchants.merchant_id FROM saq_merchants LEFT JOIN merchants ON saq_merchants.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM system_transactions WHERE merchant_id in (SELECT system_transactions.merchant_id FROM system_transactions LEFT JOIN merchants ON system_transactions.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM tgates WHERE merchant_id in (SELECT tgates.merchant_id FROM tgates LEFT JOIN merchants ON tgates.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM timeline_entries WHERE merchant_id in (SELECT timeline_entries.merchant_id FROM timeline_entries LEFT JOIN merchants ON timeline_entries.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM uw_approvalinfo_merchant_xrefs WHERE merchant_id in (SELECT uw_approvalinfo_merchant_xrefs.merchant_id FROM uw_approvalinfo_merchant_xrefs LEFT JOIN merchants ON uw_approvalinfo_merchant_xrefs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM uw_infodoc_merchant_xrefs WHERE merchant_id in (SELECT uw_infodoc_merchant_xrefs.merchant_id FROM uw_infodoc_merchant_xrefs LEFT JOIN merchants ON uw_infodoc_merchant_xrefs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM uw_status_merchant_xrefs WHERE merchant_id in (SELECT uw_status_merchant_xrefs.merchant_id FROM uw_status_merchant_xrefs LEFT JOIN merchants ON uw_status_merchant_xrefs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_references WHERE merchant_id in (SELECT merchant_references.merchant_id FROM merchant_references LEFT JOIN merchants ON merchant_references.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_rejects WHERE merchant_id in (SELECT merchant_rejects.merchant_id FROM merchant_rejects LEFT JOIN merchants ON merchant_rejects.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM merchant_uws WHERE merchant_id in (SELECT merchant_uws.merchant_id FROM merchant_uws LEFT JOIN merchants ON merchant_uws.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM onlineapp_epayments WHERE merchant_id in (SELECT onlineapp_epayments.merchant_id FROM onlineapp_epayments LEFT JOIN merchants ON onlineapp_epayments.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM onlineapp_multipasses WHERE merchant_id in (SELECT onlineapp_multipasses.merchant_id FROM onlineapp_multipasses LEFT JOIN merchants ON onlineapp_multipasses.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM orders WHERE merchant_id in (SELECT orders.merchant_id FROM orders LEFT JOIN merchants ON orders.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM products_and_services WHERE merchant_id in (SELECT products_and_services.merchant_id FROM products_and_services LEFT JOIN merchants ON products_and_services.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM residual_pricings WHERE merchant_id in (SELECT residual_pricings.merchant_id FROM residual_pricings LEFT JOIN merchants ON residual_pricings.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM residual_reports WHERE merchant_id in (SELECT residual_reports.merchant_id FROM residual_reports LEFT JOIN merchants ON residual_reports.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM saq_control_scans WHERE merchant_id in (SELECT saq_control_scans.merchant_id FROM saq_control_scans LEFT JOIN merchants ON saq_control_scans.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM saq_control_scan_unboardeds WHERE merchant_id in (SELECT saq_control_scan_unboardeds.merchant_id FROM saq_control_scan_unboardeds LEFT JOIN merchants ON saq_control_scan_unboardeds.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM saq_merchants WHERE merchant_id in (SELECT saq_merchants.merchant_id FROM saq_merchants LEFT JOIN merchants ON saq_merchants.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM system_transactions WHERE merchant_id in (SELECT system_transactions.merchant_id FROM system_transactions LEFT JOIN merchants ON system_transactions.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM tgates WHERE merchant_id in (SELECT tgates.merchant_id FROM tgates LEFT JOIN merchants ON tgates.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM timeline_entries WHERE merchant_id in (SELECT timeline_entries.merchant_id FROM timeline_entries LEFT JOIN merchants ON timeline_entries.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM uw_approvalinfo_merchant_xrefs WHERE merchant_id in (SELECT uw_approvalinfo_merchant_xrefs.merchant_id FROM uw_approvalinfo_merchant_xrefs LEFT JOIN merchants ON uw_approvalinfo_merchant_xrefs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM uw_infodoc_merchant_xrefs WHERE merchant_id in (SELECT uw_infodoc_merchant_xrefs.merchant_id FROM uw_infodoc_merchant_xrefs LEFT JOIN merchants ON uw_infodoc_merchant_xrefs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM uw_status_merchant_xrefs WHERE merchant_id in (SELECT uw_status_merchant_xrefs.merchant_id FROM uw_status_merchant_xrefs LEFT JOIN merchants ON uw_status_merchant_xrefs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM virtual_checks WHERE merchant_id in (SELECT virtual_checks.merchant_id FROM virtual_checks LEFT JOIN merchants ON virtual_checks.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM virtual_check_webs WHERE merchant_id in (SELECT virtual_check_webs.merchant_id FROM virtual_check_webs LEFT JOIN merchants ON virtual_check_webs.merchant_id = merchants.merchant_id where merchants.merchant_id is null);
 DELETE FROM webpasses WHERE merchant_id in (SELECT webpasses.merchant_id FROM webpasses LEFT JOIN merchants ON webpasses.merchant_id = merchants.merchant_id where merchants.merchant_id is null);

-----------------  UPDATE -------------- EXTRA TEST ----------------------------
 UPDATE achs SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = achs.merchant_id);
 UPDATE addresses SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = addresses.merchant_id);
 UPDATE amexes SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = amexes.merchant_id);
 UPDATE authorizes SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = authorizes.merchant_id);
 UPDATE bankcards SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = bankcards.merchant_id);
 UPDATE check_guarantees SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = check_guarantees.merchant_id);
 UPDATE commission_pricings SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = commission_pricings.merchant_id);
 UPDATE commission_reports SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = commission_reports.merchant_id);
 UPDATE debits SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = debits.merchant_id);
 UPDATE discovers SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = discovers.merchant_id);
 UPDATE ebts SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = ebts.merchant_id);
 UPDATE equipment_programmings SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = equipment_programmings.merchant_id);
 UPDATE gateway0s SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = gateway0s.merchant_id);
 UPDATE gateway1s SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = gateway1s.merchant_id);
 UPDATE gateway2s SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = gateway2s.merchant_id);
 UPDATE gift_cards SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = gift_cards.merchant_id);
 UPDATE last_deposit_reports SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = last_deposit_reports.merchant_id);
 UPDATE merchant_achs SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_achs.merchant_id);
 UPDATE merchant_banks SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_banks.merchant_id);
 UPDATE merchant_cancellations SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_cancellations.merchant_id);
 UPDATE merchant_card_types SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_card_types.merchant_id);
 UPDATE merchant_changes SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_changes.merchant_id);
 UPDATE merchant_notes SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_notes.merchant_id);
 UPDATE merchant_owners SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_owners.merchant_id);
 UPDATE merchant_pcis SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_pcis.merchant_id);
 UPDATE merchant_references SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_references.merchant_id);
 UPDATE merchant_rejects SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_rejects.merchant_id);
 UPDATE merchant_uws SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = merchant_uws.merchant_id);
 UPDATE onlineapp_epayments SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = onlineapp_epayments.merchant_id);
 UPDATE orders SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = orders.merchant_id);
 UPDATE orderitems SET item_merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = orderitems.item_merchant_id);
 UPDATE products_and_services SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = products_and_services.merchant_id);
 UPDATE residual_pricings SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = residual_pricings.merchant_id);
 UPDATE residual_reports SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = residual_reports.merchant_id);
 UPDATE saq_control_scans SET merchant_id = (select id from merchants where substring(merchants.merchant_id::text FROM 5 for 12) = substring(saq_control_scans.merchant_id::text from 5 for 12));
 UPDATE saq_control_scan_unboardeds SET merchant_id = (select id from merchants where substring(merchants.merchant_id::text FROM 5 for 12) = substring(saq_control_scan_unboardeds.merchant_id::text from 5 for 12));
 UPDATE saq_merchants SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = saq_merchants.merchant_id);
 UPDATE system_transactions SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = system_transactions.merchant_id);
 UPDATE tgates SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = tgates.merchant_id);
 UPDATE timeline_entries SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = timeline_entries.merchant_id);
 UPDATE users SET user_type_id = (SELECT id FROM user_types where users.user_type_id = user_types.user_type);
 UPDATE uw_approvalinfo_merchant_xrefs SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = uw_approvalinfo_merchant_xrefs.merchant_id);
 UPDATE uw_infodoc_merchant_xrefs SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = uw_infodoc_merchant_xrefs.merchant_id);
 UPDATE uw_status_merchant_xrefs SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = uw_status_merchant_xrefs.merchant_id);
 UPDATE virtual_checks SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = virtual_checks.merchant_id);
 UPDATE virtual_check_webs SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = virtual_check_webs.merchant_id);
 UPDATE webpasses SET merchant_id = (SELECT id FROM merchants where  merchants.merchant_id = webpasses.merchant_id);
END;
BEGIN;
------------------------------------
--UUID id matching updates    
------------------------------------
 UPDATE onlineapp_epayments SET user_id = (SELECT id FROM onlineapp_users where onlineapp_users.user_id = onlineapp_epayments.user_id);
 UPDATE merchant_uws SET final_approved_id = (SELECT id FROM merchant_uw_final_approveds where merchant_uw_final_approveds.merchant_uw_final_approved_id = merchant_uws.final_approved_id);
 UPDATE addresses SET address_type_id = (SELECT id FROM address_types where address_types.address_type = addresses.address_type_id);
 UPDATE merchant_card_types SET card_type = (SELECT id FROM card_types where card_types.card_type = merchant_card_types.card_type);
 UPDATE debits SET acquirer_id = (SELECT id FROM debit_acquirers where debit_acquirers.debit_acquirer_id = debits.acquirer_id);
 UPDATE users SET entity_id = (SELECT id FROM entities where users.entity_id = entities.entity);
 UPDATE merchants SET entity_id = (SELECT id FROM entities where merchants.entity_id = entities.entity);
 UPDATE admin_entity_views SET entity_id = (SELECT id FROM entities where admin_entity_views.entity_id = entities.entity);
 UPDATE equipment_items SET equipment_type = (SELECT id FROM equipment_types where equipment_items.equipment_type = equipment_types.equipment_type_old_id);
 UPDATE orderitems_replacements SET orderitem_id = (SELECT id FROM orderitems where orderitems_replacements.orderitem_id = orderitems.orderitem_id);
 UPDATE orderitems SET equipment_item_id = (SELECT id FROM equipment_items where orderitems.equipment_item_id = equipment_items.equipment_item_old_id);
 UPDATE equipment_programming_type_xrefs SET equipment_programming_id = (SELECT id FROM equipment_programmings where equipment_programmings.programming_id = equipment_programming_type_xrefs.equipment_programming_id);
 UPDATE orderitems SET equipment_type_id = (SELECT id FROM equipment_types where orderitems.equipment_type_id = equipment_types.equipment_type_old_id);
 UPDATE bankcards SET gateway_id = (SELECT id FROM gateways where bankcards.gateway_id = gateways.old_id);
 UPDATE discovers SET gateway_id = (SELECT id FROM gateways where discovers.gateway_id = gateways.old_id);
 UPDATE equipment_programmings SET gateway_id = (SELECT id FROM gateways where equipment_programmings.gateway_id = gateways.old_id);
 UPDATE gateway1s SET gateway_id = (SELECT id FROM gateways where gateway1s.gateway_id = gateways.old_id);
 UPDATE gateway2s SET gateway_id = (SELECT id FROM gateways where gateway2s.gateway_id = gateways.old_id);
 UPDATE merchants SET group_id = (SELECT id FROM groups where merchants.group_id = groups.group_old_id);
 UPDATE merchant_changes SET change_type_id = (SELECT id FROM change_types where merchant_changes.change_type_id = CAST(change_types.change_type_old_id AS Text));
 UPDATE merchant_changes SET approved_by_user_id = (SELECT id FROM users where merchant_changes.approved_by_user_id = users.user_id);
 UPDATE merchant_changes SET user_id = (SELECT id FROM users where merchant_changes.user_id = users.user_id); 
 UPDATE merchant_changes SET merchant_note_id = (SELECT id FROM merchant_notes where merchant_changes.merchant_note_id = merchant_notes.merchant_note_id); 
 UPDATE system_transactions SET change_id = (SELECT id FROM merchant_changes where system_transactions.change_id = merchant_changes.change_id); 
 UPDATE system_transactions SET merchant_note_id = (SELECT id FROM merchant_notes where system_transactions.merchant_note_id = merchant_notes.merchant_note_id); 
 UPDATE rep_partner_xrefs SET partner_id = (SELECT id FROM partners where rep_partner_xrefs.partner_id = partners.partner_old_id);
 UPDATE residual_reports SET partner_id = (SELECT id FROM partners where residual_reports.partner_id = partners.partner_old_id);
 UPDATE commission_reports SET partner_id = (SELECT id FROM partners where commission_reports.partner_id = partners.partner_old_id);
 UPDATE merchants SET partner_id = (SELECT id FROM partners where merchants.partner_id = partners.partner_old_id);
 UPDATE pci_compliances SET pci_compliance_date_type_id = (SELECT id FROM pci_compliance_date_types where pci_compliances.pci_compliance_date_type_id = pci_compliance_date_types.old_id);
 UPDATE pci_compliances SET saq_merchant_id = (SELECT id FROM saq_merchants where pci_compliances.saq_merchant_id = saq_merchants.old_id);
 UPDATE users u SET parent_user_id = (SELECT usrs.id FROM (SELECT id,user_id from users) usrs where usrs.user_id = u.parent_user_id);
 UPDATE users u SET secondary_parent_user_id = (SELECT usrs.id FROM (SELECT id,user_id from users) usrs where usrs.user_id = u.secondary_parent_user_id);
 UPDATE rep_product_profit_pcts SET user_id = (SELECT id FROM users where rep_product_profit_pcts.user_id = users.user_id);
 UPDATE rep_product_profit_pcts SET products_services_type_id = (SELECT id FROM products_services_types where rep_product_profit_pcts.products_services_type_id = products_services_types.products_services_type_old_id);
 UPDATE referer_products_services_xrefs SET products_services_type_id = (SELECT id FROM products_services_types where referer_products_services_xrefs.products_services_type_id = products_services_types.products_services_type_old_id);
 UPDATE products_and_services SET products_services_type_id = (SELECT id FROM products_services_types where products_and_services.products_services_type_id = products_services_types.products_services_type_old_id);
 UPDATE residual_reports SET products_services_type_id = (SELECT id FROM products_services_types where residual_reports.products_services_type_id = products_services_types.products_services_type_old_id);
 UPDATE residual_pricings SET products_services_type_id = (SELECT id FROM products_services_types where residual_pricings.products_services_type_id = products_services_types.products_services_type_old_id);
 UPDATE commission_pricings SET products_services_type_id = (SELECT id FROM products_services_types where commission_pricings.products_services_type_id = products_services_types.products_services_type_old_id);
 UPDATE discover_user_bet_tables SET user_id = (SELECT id FROM users where discover_user_bet_tables.user_id = users.user_id);
 UPDATE adjustments SET user_id = (SELECT id FROM users where adjustments.user_id = users.user_id);
 UPDATE admin_entity_views SET user_id = (SELECT id FROM users where admin_entity_views.user_id = users.user_id);
 UPDATE merchant_notes SET user_id = (SELECT id FROM users where merchant_notes.user_id = users.user_id);
 UPDATE merchants SET user_id = (SELECT id FROM users where merchants.user_id = users.user_id);
 UPDATE orders SET user_id = (SELECT id FROM users where orders.user_id = users.user_id);
 UPDATE pricing_matrices SET user_id = (SELECT id FROM users where pricing_matrices.user_id = users.user_id);
 UPDATE rep_cost_structures SET user_id = (SELECT id FROM users where rep_cost_structures.user_id = users.user_id);
 UPDATE residual_reports SET user_id = (SELECT id FROM users where residual_reports.user_id = users.user_id);
 UPDATE system_transactions SET user_id = (SELECT id FROM users where system_transactions.user_id = users.user_id);
 UPDATE user_bet_tables SET user_id = (SELECT id FROM users where user_bet_tables.user_id = users.user_id);
 UPDATE rep_partner_xrefs SET mgr1_id = (SELECT id FROM users where rep_partner_xrefs.mgr1_id = users.user_id);
 UPDATE rep_partner_xrefs SET mgr2_id = (SELECT id FROM users where rep_partner_xrefs.mgr2_id = users.user_id);
 UPDATE rep_partner_xrefs SET user_id = (SELECT id FROM users where rep_partner_xrefs.user_id = users.user_id);
 UPDATE merchants SET merchant_acquirer_id = (SELECT id FROM merchant_acquirers where merchants.merchant_acquirer_id = merchant_acquirers.acquirer_id);
 UPDATE merchants SET merchant_bin_id = (SELECT id FROM merchant_bins where merchants.merchant_bin_id = merchant_bins.bin_id);
 UPDATE merchants SET cancellation_fee_id = (SELECT id FROM cancellation_fees where merchants.cancellation_fee_id = cancellation_fees.cancellation_fee_id);
 UPDATE merchant_cancellations SET subreason_id = (SELECT id FROM merchant_cancellation_subreasons where merchant_cancellations.subreason_id = merchant_cancellation_subreasons.old_id);
 UPDATE addresses SET merchant_owner_id = (SELECT id FROM merchant_owners where addresses.merchant_owner_id = merchant_owners.owner_id);
 UPDATE merchant_reject_lines SET reject_id = (SELECT id FROM merchant_rejects where merchant_reject_lines.reject_id = merchant_rejects.old_id);
 UPDATE merchant_reject_lines SET status_id = (SELECT id FROM merchant_reject_statuses where merchant_reject_lines.status_id = merchant_reject_statuses.old_id); 
 UPDATE merchant_rejects SET recurrance_id = (SELECT id FROM merchant_reject_recurrances where merchant_rejects.recurrance_id = merchant_reject_recurrances.old_id);
 UPDATE merchant_rejects SET type_id = (SELECT id FROM merchant_reject_types where merchant_rejects.type_id = merchant_reject_types.old_id);
 UPDATE merchant_uws SET final_status_id = (SELECT id FROM merchant_uw_final_statuses where merchant_uws.final_status_id = merchant_uw_final_statuses.old_id);
 UPDATE merchant_notes SET note_type_id = (SELECT id FROM note_types where merchant_notes.note_type_id = note_types.note_type_id);
 UPDATE merchants SET app_id = (SELECT id FROM onlineapp_applications where merchants.app_id = onlineapp_applications.old_id);
 UPDATE onlineapp_coversheets SET app_id = (SELECT id FROM onlineapp_applications where onlineapp_coversheets.app_id = onlineapp_applications.old_id);
 UPDATE onlineapp_email_timelines SET app_id = (SELECT id FROM onlineapp_applications where onlineapp_email_timelines.app_id = onlineapp_applications.old_id);
 UPDATE onlineapp_epayments SET app_id = (SELECT id FROM onlineapp_applications where onlineapp_epayments.app_id = onlineapp_applications.old_id);
 UPDATE onlineapp_multipasses SET application_id = (SELECT id FROM onlineapp_applications where onlineapp_multipasses.application_id = onlineapp_applications.old_id);
 UPDATE onlineapp_email_timelines SET subject_id = (SELECT id FROM onlineapp_email_timeline_subjects where onlineapp_email_timelines.subject_id = onlineapp_email_timeline_subjects.old_id);
 UPDATE onlineapp_users SET group_id = (SELECT id FROM onlineapp_groups where onlineapp_users.group_id = onlineapp_groups.old_id);
 UPDATE onlineapp_applications SET user_id = (SELECT id FROM onlineapp_users where onlineapp_applications.user_id = onlineapp_users.user_id);
 UPDATE onlineapp_coversheets SET user_id = (SELECT id FROM onlineapp_users where onlineapp_coversheets.user_id = onlineapp_users.user_id);
 UPDATE onlineapp_apips SET user_id = (SELECT id FROM onlineapp_users where onlineapp_apips.user_id = onlineapp_users.user_id);
 UPDATE orderitems SET type_id = (SELECT id FROM orderitem_types where orderitems.type_id = orderitem_types.orderitem_type_id);
 UPDATE merchants SET ref_seq_number = (SELECT id FROM referers where merchants.ref_seq_number = referers.ref_seq_number);
 UPDATE referers_bets SET ref_seq_number = (SELECT id FROM referers where referers_bets.ref_seq_number = referers.ref_seq_number);
 UPDATE referer_products_services_xrefs SET ref_seq_number = (SELECT id FROM referers where referer_products_services_xrefs.ref_seq_number = referers.ref_seq_number);
 UPDATE saq_prequalifications SET saq_merchant_id = (SELECT id FROM saq_merchants where saq_prequalifications.saq_merchant_id = saq_merchants.old_id);
 UPDATE saq_merchant_pci_email_sents SET saq_merchant_id = (SELECT id FROM saq_merchants where saq_merchant_pci_email_sents.saq_merchant_id = saq_merchants.old_id);
 END;
 BEGIN;
 UPDATE saq_merchant_survey_xrefs SET saq_merchant_id = (SELECT id FROM saq_merchants where saq_merchant_survey_xrefs.saq_merchant_id = saq_merchants.old_id);
 UPDATE saq_merchant_survey_xrefs smsx set saq_confirmation_survey_id = (SELECT ses.id FROM (SELECT id,old_id FROM saq_merchant_survey_xrefs) ses where ses.old_id = smsx.saq_confirmation_survey_old_id);
 UPDATE saq_merchant_survey_xrefs smsx set saq_eligibility_survey_id = (SELECT ses.id FROM (SELECT id,old_id FROM saq_merchant_survey_xrefs) ses where ses.old_id = smsx.saq_eligibility_survey_old_id);
 UPDATE saq_answers SET saq_merchant_survey_xref_id = (SELECT id FROM saq_merchant_survey_xrefs where saq_answers.saq_merchant_survey_xref_id = saq_merchant_survey_xrefs.old_id);
 END;
 BEGIN;
 UPDATE saq_merchant_pci_email_sents SET saq_merchant_pci_email_id = (SELECT id FROM saq_merchant_pci_emails where saq_merchant_pci_email_sents.saq_merchant_pci_email_id = saq_merchant_pci_emails.old_id);
 UPDATE saq_survey_question_xrefs SET saq_question_id = (SELECT id FROM saq_questions where saq_survey_question_xrefs.saq_question_id = saq_questions.old_id);
 UPDATE saq_survey_question_xrefs SET saq_survey_id = (SELECT id FROM saq_surveys where saq_survey_question_xrefs.saq_survey_id = saq_surveys.old_id);
 UPDATE saq_surveys SET eligibility_survey_id = (SELECT id FROM saq_surveys where saq_surveys.eligibility_survey_id = saq_surveys.old_id);
 UPDATE saq_surveys SET confirmation_survey_id = (SELECT id FROM saq_surveys where saq_surveys.confirmation_survey_id = saq_surveys.old_id);
 UPDATE saq_merchant_survey_xrefs SET saq_survey_id = (SELECT id FROM saq_surveys where saq_merchant_survey_xrefs.saq_survey_id = saq_surveys.old_id);
 UPDATE saq_answers SET saq_survey_question_xref_id = (SELECT id FROM saq_survey_question_xrefs where saq_answers.saq_survey_question_xref_id = saq_survey_question_xrefs.old_id);
 UPDATE bankcards SET bc_usaepay_rep_gtwy_add_cost_id = (SELECT id FROM usaepay_rep_gtwy_add_costs where bankcards.bc_usaepay_rep_gtwy_add_cost_id = usaepay_rep_gtwy_add_costs.old_id);
 UPDATE gateway0s SET gw_usaepay_rep_gtwy_add_cost_id = (SELECT id FROM usaepay_rep_gtwy_add_costs where gateway0s.gw_usaepay_rep_gtwy_add_cost_id = usaepay_rep_gtwy_add_costs.old_id);
 UPDATE gateway0s SET gw_usaepay_rep_gtwy_cost_id = (SELECT id FROM usaepay_rep_gtwy_costs where gateway0s.gw_usaepay_rep_gtwy_cost_id = usaepay_rep_gtwy_costs.old_id);
 UPDATE bankcards SET bc_usaepay_rep_gtwy_cost_id = (SELECT id FROM usaepay_rep_gtwy_costs where bankcards.bc_usaepay_rep_gtwy_cost_id = usaepay_rep_gtwy_costs.old_id);
 UPDATE pricing_matrices SET user_type_id = (SELECT id FROM user_types where pricing_matrices.user_type_id = user_types.user_type);
 UPDATE uw_approvalinfo_merchant_xrefs SET approvalinfo_id = (SELECT id FROM uw_approvalinfos where uw_approvalinfo_merchant_xrefs.approvalinfo_id = uw_approvalinfos.old_id);
 UPDATE uw_infodoc_merchant_xrefs SET infodoc_id = (SELECT id FROM uw_infodocs where uw_infodoc_merchant_xrefs.infodoc_id = uw_infodocs.old_id);
 UPDATE uw_infodoc_merchant_xrefs SET received_id = (SELECT id FROM uw_receiveds where uw_infodoc_merchant_xrefs.received_id = uw_receiveds.old_id);
 UPDATE uw_status_merchant_xrefs SET status_id = (SELECT id FROM uw_statuses where uw_status_merchant_xrefs.status_id = uw_statuses.old_id);
 UPDATE uw_approvalinfo_merchant_xrefs SET verified_option_id = (SELECT id FROM uw_verified_options where uw_approvalinfo_merchant_xrefs.verified_option_id = uw_verified_options.old_id);
 UPDATE onlineapp_api_logs SET user_id = (SELECT id FROM users where onlineapp_api_logs.user_id = users.user_id);
 UPDATE user_bet_tables SET bet_id = (SELECT id FROM bets where user_bet_tables.bet_code = bets.bet_code);
 UPDATE user_bet_tables SET network_id = (SELECT id FROM networks where user_bet_tables.network_id = networks.network_id);
 UPDATE discover_user_bet_tables SET bet_id = (SELECT id FROM discover_bets where discover_user_bet_tables.bet_code = discover_bets.bet_code);
 UPDATE discover_user_bet_tables SET network_id = (SELECT id FROM networks where discover_user_bet_tables.network_id = networks.network_id);

-----------------------------------------------------------------------------------------------------------------------------------------------
-- The sample query below helps fill in data for a specific column that failed to migrate its data or got deleted for whatever reason.
-- This helps avoid having to drop and remigrate the entire table. 
-- It matches old id's from the table in the legacy database and the new database where the data is being inserted.
-----------------------------------------------------------------------------------------------------------------------------------------------
-- \timing
-- select dblink_connect_u('pg84', 'dbname=axia');
-- UPDATE users u SET parent_user_id = (SELECT usr.parent_user FROM dblink('pg84','SELECT user_id,parent_user from "user"') 
-- as usr(user_id integer,parent_user integer) 
-- where usr.user_id = u.user_id::int);
-----------------------------------------------------------------------------------------------------------------------------------------------

-- UPDATE destinationTBL SET destinationCOL = (SELECT id FROM SRCTable where destinationTBL.destinationCOL = SRCTBL.SRCCOL);

-- The cakephp framework hashes passwords differently fron md5 hash.
-- Passwords need to be moved to password_2 column to allow for cakephp to place the those 
-- passwords back into the password column with the new hashing method when a user logs into the website.

update users set password_2 = password;
update users set password = null;


END;
