<?php

namespace common\models\traits;
use common\models\Servicecentreservice;
/* @var $this Servicecentreservice */
/* @property Servicecentreservice[] $services */
trait Servicecentreservicetrait
{

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Servicecentreservice::class, ['IdServiceCentre' => 'Id']);
    }

    public function getServicesStatus(){
        try {
            $response = [];
            foreach ($this->services as $service){
                $response[$service->Code] = $service->getServiceStatus();
            }
            return ['success' => true,'values'=>[$this->Id => $response]];
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getAllServicesStatus(){
        try {
            $response = [];
            $centres = self::find()
                ->joinWith('state b')
                ->joinWith('type c')
                ->where([
                    'b.Code' => self::STATE_ACTIVE,
                ])
                ->andWhere('(c.Code IN(:duisite, :datacenter))',[':duisite' => self::TYPE_DUISITE,':datacenter' => self::TYPE_DATACENTER])
                ->all();
            foreach ($centres as $centre){
                $result = [];
                foreach ($centre->services as  $service){
                    $result[$service->Code] = $service->getServiceStatus();
                }
                $response[$centre->Id] = $result;
            }
            return ['success' => true,'values'=>$response];
        } catch (Exception $ex) {

        }
    }
}