<?php

namespace backend\models;

use backend\models\CustomActiveRecord;
use Yii;
/**
 * This is the model class for table "TRAM_VIGENTE_SDMS".
 *
 * @property int $NUM_SOLIC
 * @property string $APPLICATION_ID
 * @property string $CREATED
 * @property string $STATE
 * @property string $OPERATION
 * @property string $ENROLLED
 * @property string $ENROLLMENT_SITE_CODE
 * @property int $OUTSIDE_ENROLLMENT
 * @property int $APPROVAL_DECISION
 * @property string $APPROVAL_COMMENT
 * @property int $APPROVAL_REQUEST
 * @property string $APPROVAL_REQUEST_REASON
 * @property string $REASON_FOR_APPLICATION
 * @property string $TRANSACTION_ID
 * @property string $OBSERVATION_STATE
 * @property string $OBSERVATION_STEP1_USER
 * @property string $OBSERVATION_STEP1_TIMESTAMP
 * @property string $OBSERVATION_STEP1_DECISION
 * @property int $OBSERVATION_STEP1_REASONFP
 * @property int $OBSERVATION_STEP1_REASONBC
 * @property int $OBSERVATION_STEP1_REASOND
 * @property string $OBSERVATION_STEP1_REASONOTHER
 * @property string $OBSERVATION_STEP2_USER
 * @property string $OBSERVATION_STEP2_TIMESTAMP
 * @property string $OBSERVATION_STEP2_DECISION
 * @property string $OBSERVATION_STEP2_COMMENT
 * @property string $OBSERVATION_STEP3_USER
 * @property string $OBSERVATION_STEP3_TIMESTAMP
 * @property string $OBSERVATION_STEP3_DECISION
 * @property string $OBSERVATION_LAST_DEC_TIMESTAMP
 * @property string $FIRSTNAMES
 * @property string $LASTNAMES
 * @property string $DATE_OF_BIRTH
 * @property int $SCANNED_NAT_ACT_EXIST
 * @property int $SCANNED_BIRT_CERT_EXIST
 * @property int $SCANNED_DOCUMENTS_EXIST
 * @property int $ATTACHMENT_APP_EXISTS
 * @property int $ATTACHMENT_ENR_EXISTS
 * @property int $BC_DEPNACLI
 * @property int $BC_MUNNACLI
 * @property string $BC_LIBRO
 * @property int $BC_ANIOLIB
 * @property string $BC_FOLIO
 * @property int $BC_PARTIDA
 * @property string $BC_CORRPAT
 * @property int $BC_ROLLONUM
 * @property int $BC_ROLLOSEC
 * @property int $BC_CUADRO
 * @property string $BC_OPERATION
 * @property int $BC_TIPO_PARTIDA
 * @property string $APPLICATION_CLIENT_VERSION
 * @property string $ENROLLMENT_OFFICER_OFFICER
 * @property string $ENROLLMENT_OFFICER_GIVENNAME
 * @property string $ENROLLMENT_OFFICER_SURNAME
 * @property string $APPLICATION_ID_OLD
 * @property int $PAYABLE
 * @property int $MOBILE
 * @property string $APPROVAL_BATCH
 * @property string $ILLEGAL_ATTEMPT_TYPE
 * @property string $BC_UPDATE_REASON
 * @property string $APPROVAL_OFFICER_OFFICER
 * @property string $APPROVAL_OFFICER_GIVENNAME
 * @property string $APPROVAL_OFFICER_SURNAME
 * @property string $ENROLLMENT_OFFICER_STATION
 * @property string $APPROVAL_OFFICER_STATION
 * @property string $ENROLLED2
 * @property string $APPROVED
 * @property string $DUI
 * @property string $DOCUMENT_NUMBER
 * @property string $DELIVERED
 * @property string $ADDITIONAL_ENROLMENT_REMARK
 * @property string $DELIVERED2
 * @property int $SCANNED_DOCUMENTS_TYPED_EXIST
 *
 * ATTACHMENTS[] $aTTACHMENTSs
 * @property Tramvigente $tramvigente
 */
class Tramvigentesdms extends CustomActiveRecord
{
    public static function getDb() {
        return Yii::$app->prdduitest;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.TRAM_VIGENTE_SDMS';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['NUM_SOLIC', 'CREATED', 'STATE', 'OPERATION', 'ENROLLED', 'ENROLLMENT_SITE_CODE', 'OUTSIDE_ENROLLMENT', 'OBSERVATION_STATE', 'PAYABLE', 'MOBILE', 'DUI'], 'required'],
            [['NUM_SOLIC', 'OUTSIDE_ENROLLMENT', 'APPROVAL_DECISION', 'APPROVAL_REQUEST', 'OBSERVATION_STEP1_REASONFP', 'OBSERVATION_STEP1_REASONBC', 'OBSERVATION_STEP1_REASOND', 'SCANNED_NAT_ACT_EXIST', 'SCANNED_BIRT_CERT_EXIST', 'SCANNED_DOCUMENTS_EXIST', 'ATTACHMENT_APP_EXISTS', 'ATTACHMENT_ENR_EXISTS', 'BC_DEPNACLI', 'BC_MUNNACLI', 'BC_ANIOLIB', 'BC_PARTIDA', 'BC_ROLLONUM', 'BC_ROLLOSEC', 'BC_CUADRO', 'BC_TIPO_PARTIDA', 'PAYABLE', 'MOBILE', 'SCANNED_DOCUMENTS_TYPED_EXIST'], 'integer'],
            [['CREATED', 'ENROLLED', 'OBSERVATION_STEP1_TIMESTAMP', 'OBSERVATION_STEP2_TIMESTAMP', 'OBSERVATION_STEP3_TIMESTAMP', 'OBSERVATION_LAST_DEC_TIMESTAMP', 'DATE_OF_BIRTH', 'ENROLLED2', 'APPROVED', 'DELIVERED', 'DELIVERED2'], 'safe'],
            [['APPLICATION_ID', 'STATE', 'OPERATION', 'TRANSACTION_ID', 'APPLICATION_CLIENT_VERSION', 'ENROLLMENT_OFFICER_OFFICER', 'ENROLLMENT_OFFICER_GIVENNAME', 'ENROLLMENT_OFFICER_SURNAME', 'APPLICATION_ID_OLD', 'APPROVAL_BATCH', 'APPROVAL_OFFICER_OFFICER', 'APPROVAL_OFFICER_GIVENNAME', 'APPROVAL_OFFICER_SURNAME', 'ENROLLMENT_OFFICER_STATION', 'APPROVAL_OFFICER_STATION'], 'string', 'max' => 255],
            [['ENROLLMENT_SITE_CODE', 'BC_OPERATION'], 'string', 'max' => 10],
            [['APPROVAL_COMMENT', 'APPROVAL_REQUEST_REASON', 'REASON_FOR_APPLICATION', 'OBSERVATION_STEP1_REASONOTHER', 'OBSERVATION_STEP2_COMMENT', 'BC_UPDATE_REASON'], 'string', 'max' => 2000],
            [['OBSERVATION_STATE', 'OBSERVATION_STEP1_USER', 'OBSERVATION_STEP1_DECISION', 'OBSERVATION_STEP2_USER', 'OBSERVATION_STEP2_DECISION', 'OBSERVATION_STEP3_USER', 'OBSERVATION_STEP3_DECISION', 'FIRSTNAMES', 'LASTNAMES', 'ADDITIONAL_ENROLMENT_REMARK'], 'string', 'max' => 100],
            [['BC_LIBRO'], 'string', 'max' => 3],
            [['BC_FOLIO'], 'string', 'max' => 5],
            [['BC_CORRPAT'], 'string', 'max' => 1],
            [['ILLEGAL_ATTEMPT_TYPE'], 'string', 'max' => 30],
            [['DUI', 'DOCUMENT_NUMBER'], 'string', 'max' => 20],
            [['NUM_SOLIC'], 'unique'],
            [['NUM_SOLIC'], 'exist', 'skipOnError' => true, 'targetClass' => Tramvigente::className(), 'targetAttribute' => ['NUM_SOLIC' => 'NUM_SOLIC']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'NUM_SOLIC' => 'Num  Solic',
            'APPLICATION_ID' => 'Application  ID',
            'CREATED' => 'Created',
            'STATE' => 'State',
            'OPERATION' => 'Operation',
            'ENROLLED' => 'Enrolled',
            'ENROLLMENT_SITE_CODE' => 'Enrollment  Site  Code',
            'OUTSIDE_ENROLLMENT' => 'Outside  Enrollment',
            'APPROVAL_DECISION' => 'Approval  Decision',
            'APPROVAL_COMMENT' => 'Approval  Comment',
            'APPROVAL_REQUEST' => 'Approval  Request',
            'APPROVAL_REQUEST_REASON' => 'Approval  Request  Reason',
            'REASON_FOR_APPLICATION' => 'Reason  For  Application',
            'TRANSACTION_ID' => 'Transaction  ID',
            'OBSERVATION_STATE' => 'Observation  State',
            'OBSERVATION_STEP1_USER' => 'Observation  Step1  User',
            'OBSERVATION_STEP1_TIMESTAMP' => 'Observation  Step1  Timestamp',
            'OBSERVATION_STEP1_DECISION' => 'Observation  Step1  Decision',
            'OBSERVATION_STEP1_REASONFP' => 'Observation  Step1  Reasonfp',
            'OBSERVATION_STEP1_REASONBC' => 'Observation  Step1  Reasonbc',
            'OBSERVATION_STEP1_REASOND' => 'Observation  Step1  Reasond',
            'OBSERVATION_STEP1_REASONOTHER' => 'Observation  Step1  Reasonother',
            'OBSERVATION_STEP2_USER' => 'Observation  Step2  User',
            'OBSERVATION_STEP2_TIMESTAMP' => 'Observation  Step2  Timestamp',
            'OBSERVATION_STEP2_DECISION' => 'Observation  Step2  Decision',
            'OBSERVATION_STEP2_COMMENT' => 'Observation  Step2  Comment',
            'OBSERVATION_STEP3_USER' => 'Observation  Step3  User',
            'OBSERVATION_STEP3_TIMESTAMP' => 'Observation  Step3  Timestamp',
            'OBSERVATION_STEP3_DECISION' => 'Observation  Step3  Decision',
            'OBSERVATION_LAST_DEC_TIMESTAMP' => 'Observation  Last  Dec  Timestamp',
            'FIRSTNAMES' => 'Firstnames',
            'LASTNAMES' => 'Lastnames',
            'DATE_OF_BIRTH' => 'Date  Of  Birth',
            'SCANNED_NAT_ACT_EXIST' => 'Scanned  Nat  Act  Exist',
            'SCANNED_BIRT_CERT_EXIST' => 'Scanned  Birt  Cert  Exist',
            'SCANNED_DOCUMENTS_EXIST' => 'Scanned  Documents  Exist',
            'ATTACHMENT_APP_EXISTS' => 'Attachment  App  Exists',
            'ATTACHMENT_ENR_EXISTS' => 'Attachment  Enr  Exists',
            'BC_DEPNACLI' => 'Bc  Depnacli',
            'BC_MUNNACLI' => 'Bc  Munnacli',
            'BC_LIBRO' => 'Bc  Libro',
            'BC_ANIOLIB' => 'Bc  Aniolib',
            'BC_FOLIO' => 'Bc  Folio',
            'BC_PARTIDA' => 'Bc  Partida',
            'BC_CORRPAT' => 'Bc  Corrpat',
            'BC_ROLLONUM' => 'Bc  Rollonum',
            'BC_ROLLOSEC' => 'Bc  Rollosec',
            'BC_CUADRO' => 'Bc  Cuadro',
            'BC_OPERATION' => 'Bc  Operation',
            'BC_TIPO_PARTIDA' => 'Bc  Tipo  Partida',
            'APPLICATION_CLIENT_VERSION' => 'Application  Client  Version',
            'ENROLLMENT_OFFICER_OFFICER' => 'Enrollment  Officer  Officer',
            'ENROLLMENT_OFFICER_GIVENNAME' => 'Enrollment  Officer  Givenname',
            'ENROLLMENT_OFFICER_SURNAME' => 'Enrollment  Officer  Surname',
            'APPLICATION_ID_OLD' => 'Application  Id  Old',
            'PAYABLE' => 'Payable',
            'MOBILE' => 'Mobile',
            'APPROVAL_BATCH' => 'Approval  Batch',
            'ILLEGAL_ATTEMPT_TYPE' => 'Illegal  Attempt  Type',
            'BC_UPDATE_REASON' => 'Bc  Update  Reason',
            'APPROVAL_OFFICER_OFFICER' => 'Approval  Officer  Officer',
            'APPROVAL_OFFICER_GIVENNAME' => 'Approval  Officer  Givenname',
            'APPROVAL_OFFICER_SURNAME' => 'Approval  Officer  Surname',
            'ENROLLMENT_OFFICER_STATION' => 'Enrollment  Officer  Station',
            'APPROVAL_OFFICER_STATION' => 'Approval  Officer  Station',
            'ENROLLED2' => 'Enrolled2',
            'APPROVED' => 'Approved',
            'DUI' => 'Dui',
            'DOCUMENT_NUMBER' => 'Document  Number',
            'DELIVERED' => 'Delivered',
            'ADDITIONAL_ENROLMENT_REMARK' => 'Additional  Enrolment  Remark',
            'DELIVERED2' => 'Delivered2',
            'SCANNED_DOCUMENTS_TYPED_EXIST' => 'Scanned  Documents  Typed  Exist',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getATTACHMENTSs()
//    {
//        return $this->hasMany(ATTACHMENTS::className(), ['NUM_SOLIC' => 'NUM_SOLIC']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNUMSOLIC()
    {
        return $this->hasOne(Tramvigente::className(), ['NUM_SOLIC' => 'NUM_SOLIC']);
    }
}
