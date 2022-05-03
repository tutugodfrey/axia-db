DELETE from ach where merchant_id like '%-%';
DELETE FROM ach where merchant_id = 'BECKER';
DELETE FROM merchant WHERE merchant_id like '''%';
DELETE FROM address WHERE merchant_id like '''%';
DELETE FROM address where merchant_id LIKE 'TEST%';
DELETE FROM address where merchant_id LIKE '%+%';
DELETE FROM address where merchant_id LIKE '%-%';
DELETE FROM address where merchant_id = '';
DELETE FROM address where char_length(merchant_id) > 16;
ALTER TABLE merchant_change DROP COLUMN "programming_id ";
DELETE FROM bankcard WHERE merchant_id = 'BECKER';
DELETE FROM bankcard WHERE merchant_id like '''%';
DELETE FROM bankcard where merchant_id LIKE 'TEST%';
DELETE FROM bankcard where merchant_id LIKE '%+%';
DELETE FROM bankcard where merchant_id LIKE '%-%';
DELETE FROM bankcard where merchant_id = '';
DELETE FROM bankcard where char_length(merchant_id) > 16;
DELETE FROM bankcard where bc_mid is NULL;
DELETE FROM check_guarantee WHERE merchant_id = 'BECKER';
DELETE FROM commission_pricing where merchant_id LIKE '%+%';
DELETE FROM commission_report WHERE merchant_id = 'BECKER';
DELETE FROM commission_report WHERE merchant_id like '''%';
DELETE FROM commission_report where merchant_id LIKE 'TEST%';
DELETE FROM commission_report where merchant_id LIKE '%+%';
DELETE FROM commission_report where merchant_id LIKE '%-%';
DELETE FROM commission_report where merchant_id = '';
DELETE FROM commission_report where char_length(merchant_id) > 16;
DROP TABLE commission_report_old CASCADE;
DELETE FROM commission_reports WHERE ach_seq_number in (30026,28536,23219,22404,21847,21615,20072,19464,19265,17736,17636,17635,17634,17528,17520,12767,17873,3017);

 ALTER TABLE merchant_reject_lines DROP CONSTRAINT merchant_reject_line_statusid_fkey;
 ALTER TABLE merchant_uws DROP CONSTRAINT merchant_uw_final_approved_id_fkey;
 ALTER TABLE merchant_uws DROP CONSTRAINT merchant_uw_final_status_id_fkey;
 ALTER TABLE onlineapp_apips DROP CONSTRAINT onlineapp_apips_user_id_fkey;
 ALTER TABLE onlineapp_email_timelines DROP CONSTRAINT onlineapp_email_timelines_app_id_fkey;
 ALTER TABLE onlineapp_email_timelines DROP CONSTRAINT onlineapp_email_timelines_subject_id_fkey;
 ALTER TABLE onlineapp_epayments DROP CONSTRAINT onlineapp_epayments_user_id_fkey;
 ALTER TABLE pci_billings DROP CONSTRAINT pci_billing_pci_billing_type_id_fkey;
 ALTER TABLE pci_compliances DROP CONSTRAINT pci_compliance_pci_compliance_date_type_id_fkey;
 ALTER TABLE pci_compliances DROP CONSTRAINT pci_compliance_saq_merchant_id_fkey;
 ALTER TABLE saq_answers DROP CONSTRAINT fkey_saq_answer_survey_question_xref_id;
 ALTER TABLE saq_answers DROP CONSTRAINT fkey_saq_merchant_survey_xref_id;
 ALTER TABLE saq_merchant_pci_email_sents DROP CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_id_fkey;
 ALTER TABLE saq_merchant_pci_email_sents DROP CONSTRAINT saq_merchant_pci_email_sent_saq_merchant_pci_email_id_fkey;
 ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_confirmation_survey_id;
 ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_eligibility_survey_id;
 ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_merchant_id;
 ALTER TABLE saq_merchant_survey_xrefs DROP CONSTRAINT fkey_saq_merchant_survey_xref_saq_survey_id;
 ALTER TABLE saq_prequalifications DROP CONSTRAINT fkey_saq_prequalification_merchant_id;
 ALTER TABLE saq_survey_question_xrefs DROP CONSTRAINT fkey_saq_survey_question_xref_saq_question_id;
 ALTER TABLE saq_survey_question_xrefs DROP CONSTRAINT fkey_saq_survey_question_xref_saq_survey_id;
 ALTER TABLE saq_surveys DROP CONSTRAINT fkey_saq_survey_confirmation_survey_id;
 ALTER TABLE saq_surveys DROP CONSTRAINT fkey_saq_survey_eligibility_survey_id;

DELETE FROM timeline_entries where merchant_id in (3948000031001243
,3948000031001273
,3948000031001645
,3948000031000778
,3948000000000000
,3948000031001688
,3948000030000051
,3948000031001529
,3948000031000652
,76411100424001064
,3948000031001460
,4541619740008148
,3948000031000615
,3948000031001073
,39480000310028961
,3948000031001244
,3948000031001633
,3948000031001503
,3948000031001636
,3948000031003770
,3948000031000800
,3948000031001072
,3948906204001224
)



CREATE DATABASE axia OWNER axia;
GRANT ALL ON DATABASE axia to axia;

\c axia
CREATE EXTENSION dblink;
GRANT EXECUTE on FUNCTION dblink_connect_u(text,text) to axia;


select distinct(a.merchant_id)
from timeline_entries a
LEFT JOIN merchants m on m.merchant_id = a.merchant_id
where m.merchant_id is null;

  764111004241054,
 7641110042401510,
 3948906204001235,
 3948000031001243,
 3948000031001273,
 3948000031001645,
  764111042403161,
 3948000000000000,
  764111042402276,
 3948000031001688,
 3948000031001529,
 3948000031001460,
  764111004241020,
  764111004240274,
 4541619740008148,
 3948000031000615,
 3948000031001073,
 3948000031001244,
 3948000031001633,
 3948000031001503,
 3948000031001636,
 3948000031003770,
 3948000031000800,
 3948000031001072,
 3948906204001224,
 3948000031000778,
  764111004240163,
 3948000030000051,
 3948000031000652,

7641000031004270,
3948906204001235,
7641440042401434,
394890620400973,
764111004242220,
7641906204000836,
7641000031000865,
7641110042402628,39480000310028961,