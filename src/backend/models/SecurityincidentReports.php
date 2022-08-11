<?php
namespace backend\models;

/**
 * Description of SecurityincidentReports
 *
 * @author avelare
 */
use Yii;
use Exception;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use backend\models\Settingdetail;
use backend\models\Securityincident;
use backend\models\Incidentcategory;
use common\models\Servicecentre;
use common\models\State;
use common\models\Type;

class SecurityincidentReports extends Securityincident {
    public $dateStart = null;
    public $dateEnd = null;
    public function getTotalEvents($criteria = []){
        try {
            $query = new Query();
            $query->from(self::tableName()." a");
            $query->innerJoin(Type::tableName().' b', 'b.Id = a.IdType');
            $query->select(['b.Name name','b.Value','count(1) y']);
            $query->where([
                'b.KeyWord' => StringHelper::basename(Securityincident::class),
            ]);
            if($this->Year){
                $query->andWhere("date_format(a.IncidentDate,'%Y') = :year", [':year'=> $this->Year]);
            }
            if($this->IdServiceCentre){
                $query->andWhere('a.IdServiceCentre = :serv', [':serv'=> $this->IdServiceCentre]);
            }
            $dateStart = $this->dateStart ? \DateTime::createFromFormat('d-m-Y', $this->dateStart) : null;
            $dateEnd = $this->dateEnd ? \DateTime::createFromFormat('d-m-Y', $this->dateEnd) : null;
            if($this->dateStart && $this->dateEnd){
                $query->andWhere("date_format(a.IncidentDate,'%Y-%m-%d') BETWEEN :datestart AND :dateend  ", [':datestart'=> $dateStart->format('Y-m-d'),':dateend' => $dateEnd->format('Y-m-d')]);
            } elseif($this->dateStart && !$this->dateEnd){
                $query->andWhere("date_format(a.IncidentDate,'%Y-%m-%d') >= :datestart ", [':datestart'=> $dateStart->format('Y-m-d'),]);
            } elseif(!$this->dateStart && $this->dateEnd){
                $query->andWhere("date_format(a.IncidentDate,'%Y-%m-%d') <= :dateend ", [':dateend'=> $dateEnd->format('Y-m-d'),]);
            }
            $query->groupBy(["b.Name"]);
            $query->orderBy(['b.Id' => SORT_ASC]);
            $result = $query->all();
            $values = [];
            $labels = [];
            foreach ($result as $val){
                $values[] = ['name' => !empty($val['Value']) ? $val['Value']:$val['name'],'title' => $val['name'], 'y' => (int) $val['y']];
            }
            return $values;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getTotalByServiceCentre($criteria = []){
        try {
            $leftParams = [];
            $response = [];
            $dataset = [];
            $categories = [];
            $drilldown = ["series"=>[]];
            $dateStart = $this->dateStart ? \DateTime::createFromFormat('d-m-Y', $this->dateStart) : null;
            $dateEnd = $this->dateEnd ? \DateTime::createFromFormat('d-m-Y', $this->dateEnd) : null;
            
            $colors = [];
            $colors_set = Settingdetail::find()
                    ->joinWith('setting b')
                    ->where(['b.KeyWord'=>'Servicecentre','b.Code'=>'COLORS'])
                    ->orderBy(['Sort' => SORT_ASC])
                    ->asArray()->all();
            
            foreach ($colors_set as $c){
                $colors[]=$c["Value"];
            }
            
            $queryCentres = new Query();
            $queryCentres->select(['a.Id','a.Name']);
            $queryCentres->from(Servicecentre::tableName().' a')
                    ->innerJoin(State::tableName().' b','b.Id = a.IdState')
                    ->innerJoin(Type::tableName().' c','c.Id = a.IdType')
                    ->where([
                        #'b.Code' => Servicecentres::STATE_ACTIVE
                        'c.Code' => [Servicecentre::TYPE_DUISITE, Servicecentre::TYPE_DATACENTER],
                    ])
                    ->andWhere('a.IdParent IS NOT NULL');
            if($this->IdServiceCentre){
                $queryCentres->andWhere('a.Id = :centre', [':centre' => $this->IdServiceCentre]);
            }
            $centres = $queryCentres->orderBy(['a.Id' => SORT_ASC])
                    ->all();
            ($this->Year ? $leftParams[':year'] = $this->Year: null);
            ($this->dateStart ? $leftParams[':datestart'] = $dateStart->format('Y-m-d'): null);
            ($this->dateEnd ? $leftParams[':dateend'] = $dateEnd->format('Y-m-d'): null);
            
            foreach ($centres as  $centre){
                $i = 0;
                $leftCondition = "";
                $query = new Query();
                $query->from(Type::tableName().' a')
                        ->select(['a.Id', 'a.Name', 'count(c.Id) Quantity'])
                        ->innerJoin(State::tableName().' b','b.Id = a.IdState');
                ($this->Year ? $leftCondition .= " AND (date_format(c.IncidentDate,'%Y') = :year )" : null);
                (($this->dateStart && $this->dateEnd) ? $leftCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') BETWEEN :datestart AND :dateend ) ": null );
                (($this->dateStart && !$this->dateEnd) ? $leftCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') >= :datestart ) ": null );
                ((!$this->dateStart && $this->dateEnd) ? $leftCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') <= :dateend ) ": null );
                
                $query->leftJoin(self::tableName().' c', 'c.IdType = a.Id AND ( c.IdServiceCentre = '.$centre['Id']." ".$leftCondition." )");
                $leftParams[':keyword'] = StringHelper::basename(Securityincident::class);
                $leftParams[':state'] = Type::STATUS_ACTIVE;
                $query->where('b.Code  = :state AND a.KeyWord = :keyword', $leftParams);
                $query->groupBy(['a.Id', 'a.Name']);
                $query->orderBy(['a.Id' => SORT_ASC]);
                $details = $query->all();
                foreach ($details as $det){
                    $dataset[$i]['name'] = $det['Name'];
                    $dataset[$i]['color'] = $colors[$i];
                    !isset($dataset[$i]['data']) ? $dataset[$i]['data'] = []: null;
                    array_push($dataset[$i]['data'], (int) $det['Quantity']);
                    $i++;
                }
                array_push($categories, $centre['Name']);
            }
            $response["dataset"] = $dataset;
            #$response["drilldown"] = $drilldown;
            $response['categories'] = $categories;
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getTotalByType($criteria = []){
        try {
            $response = [];
            $dataset = [];
            $categories = [];
            $queryParams = [];
            $queryCondition = "";
            $dateStart = $this->dateStart ? \DateTime::createFromFormat('d-m-Y', $this->dateStart) : null;
            $dateEnd = $this->dateEnd ? \DateTime::createFromFormat('d-m-Y', $this->dateEnd) : null;
            $query = new Query();
            $query->select(['a.Id', 'a.Name']);
            $query->from(Incidentcategory::tableName().' a')
                    ->innerJoin(State::tableName().' b','b.Id = a.IdState')
                    ->where([
                        'b.Code' => Incidentcategory::STATUS_ACTIVE,
                        ]);
            $query->andWhere('a.IdParent IS NULL');
            $query->orderBy(['a.Id' => SORT_ASC]);
            $catdet = $query->all();
            
            ($this->Year ? $queryParams[':year'] = $this->Year: null);
            ($this->dateStart ? $queryParams[':datestart'] = $dateStart->format('Y-m-d'): null);
            ($this->dateEnd ? $queryParams[':dateend'] = $dateEnd->format('Y-m-d'): null);
            ($this->IdServiceCentre ? $queryParams[':centre'] = $this->IdServiceCentre: null);
            
            ($this->Year ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y') = :year )" : null);
            (($this->dateStart && $this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') BETWEEN :datestart AND :dateend ) ": null );
            (($this->dateStart && !$this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') >= :datestart ) ": null );
            ((!$this->dateStart && $this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') <= :dateend ) ": null );
            ( $this->IdServiceCentre ? $queryCondition .= " AND c.IdServiceCentre = :centre" : null);
            $queryParams[':keyword'] = StringHelper::basename(Securityincident::class);
            $queryParams[':state'] = Type::STATUS_ACTIVE;
            
            foreach ($catdet as $cat){
                array_push($categories, $cat['Name']);
                $i = 0;
                $query = new Query();
                $query->from(Type::tableName().' a')
                        ->select(['a.Id', 'a.Name', 'count(c.Id) Quantity'])
                        ->innerJoin(State::tableName().' b','b.Id = a.IdState');
                $query->leftJoin(self::tableName().' c', 'c.IdType = a.Id AND ( c.IdCategoryType = '.$cat['Id']." ".$queryCondition." )");
                $query->where('b.Code  = :state AND a.KeyWord = :keyword', $queryParams);
                $query->groupBy(['a.Id', 'a.Name']);
                $query->orderBy(['a.Id' => SORT_ASC]);
                $details = $query->all();
                foreach ($details as $det){
                    $dataset[$i]['name'] = $det['Name'];
                    #$dataset[$i]['color'] = $colors[$i];
                    !isset($dataset[$i]['data']) ? $dataset[$i]['data'] = []: null;
                    array_push($dataset[$i]['data'], (int) $det['Quantity']);
                    $i++;
                }
            }
            $response['categories'] = $categories;
            $response['dataset'] = $dataset;
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getTotalByCategory($criteria = []){
        try {
            $leftParams = [];
            $leftCondition = "";
            $query = new Query();
            $query->from(self::tableName()." a");
            $query->innerJoin(Incidentcategory::tableName().' b', 'b.Id = a.IdCategoryType');
            $query->innerJoin(State::tableName().' c', 'c.Id = b.IdState');
            $query->innerJoin(Type::tableName().' d', 'd.Id = a.IdType');
            $query->select(['b.Id','b.Name name','count(1) y']);
            $query->where([
                'c.Code' => Incidentcategory::STATUS_ACTIVE,
                'd.KeyWord' => StringHelper::basename(Securityincident::class),
            ]);
            if($this->Year){
                $query->andWhere("date_format(a.IncidentDate,'%Y') = :year", [':year'=> $this->Year]);
                $leftCondition .= " AND date_format(c.IncidentDate,'%Y') = :year";
                $leftParams[':year'] = $this->Year;
            }
            if($this->IdServiceCentre){
                $query->andWhere('a.IdServiceCentre = :serv', [':serv'=> $this->IdServiceCentre]);
                $leftCondition .= " AND a.Id = :centre";
                $leftParams[':centre'] = $this->IdServiceCentre;
            }
            $dateStart = $this->dateStart ? \DateTime::createFromFormat('d-m-Y', $this->dateStart) : null;
            $dateEnd = $this->dateEnd ? \DateTime::createFromFormat('d-m-Y', $this->dateEnd) : null;
            if($this->dateStart && $this->dateEnd){
                $query->andWhere("date_format(a.IncidentDate,'%Y-%m-%d') BETWEEN :datestart AND :dateend  ", [':datestart'=> $dateStart->format('Y-m-d'),':dateend' => $dateEnd->format('Y-m-d')]);
            } elseif($this->dateStart && !$this->dateEnd){
                $query->andWhere("date_format(a.IncidentDate,'%Y-%m-%d') >= :datestart ", [':datestart'=> $dateStart->format('Y-m-d'),]);
            } elseif(!$this->dateStart && $this->dateEnd){
                $query->andWhere("date_format(a.IncidentDate,'%Y-%m-%d') <= :dateend ", [':dateend'=> $dateEnd->format('Y-m-d'),]);
            }
            $query->groupBy(["b.Name"]);
            $query->orderBy(['b.Id' => SORT_ASC]);
            $result = $query->all();
            $response = [];
            $dataset = [];
            $drilldown = ["series"=>[]];
            
            foreach ($result as $r){
                $params = [':id'=> $r["Id"]];
                $params = array_merge($params, $leftParams);
                $query_operations = new Query();
                $query_operations->select(['a.Id','a.Name','count(c.Id) Quantity']);
                $query_operations->from(Servicecentre::tableName().' a');
                $query_operations->innerJoin(State::tableName().' b', 'b.Id = a.IdState');
                $query_operations->innerJoin(Type::tableName().' e','e.Id = a.IdType');
                $query_operations->leftJoin(self::tableName().' c', 'c.IdServiceCentre = a.Id AND c.IdCategoryType = :id '.$leftCondition, $params);
                $query_operations->leftJoin(Type::tableName().' d',"d.Id = c.IdType AND d.KeyWord = '".StringHelper::basename(Securityincident::class)."'");
                $query_operations->where([
                    #'b.Code' => Servicecentres::STATE_ACTIVE,
                    'e.Code' => [Servicecentre::TYPE_DUISITE, Servicecentre::TYPE_DATACENTER],
                ]);
                $query_operations->groupBy(['a.Id','a.Name']);
                $query_operations->orderBy(['a.Id'=> SORT_ASC]);
                $details = $query_operations->all();
                
                $dataApp = [];
                foreach ($details as $det){
                    $dataApp[] = [$det['Name'], (int) $det['Quantity']];
                }
                
                $drilldown['series'][] = [
                    'name'=> $r['name'],
                    'id'=> $r['name'],
                    'colorByPoint'=>TRUE,
                    'data'=> $dataApp,
                ];
                $dataset[] = ['name'=>$r['name'],'y'=>(int)$r['y'],"drilldown"=>$r['name']];
            }
            $response['dataset'] = $dataset;
            $response['drilldown'] = $drilldown;
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getTotalByInterrupt($criteria = []){
        try {
            $queryParams = [];
            $queryCondition = "";
            $dateStart = $this->dateStart ? \DateTime::createFromFormat('d-m-Y', $this->dateStart) : null;
            $dateEnd = $this->dateEnd ? \DateTime::createFromFormat('d-m-Y', $this->dateEnd) : null;
            
            ($this->Year ? $queryParams[':year'] = $this->Year: null);
            ($this->dateStart ? $queryParams[':datestart'] = $dateStart->format('Y-m-d'): null);
            ($this->dateEnd ? $queryParams[':dateend'] = $dateEnd->format('Y-m-d'): null);
            ($this->IdServiceCentre ? $queryParams[':centre'] = $this->IdServiceCentre: null);
            
            ($this->Year ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y') = :year )" : null);
            (($this->dateStart && $this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') BETWEEN :datestart AND :dateend ) ": null );
            (($this->dateStart && !$this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') >= :datestart ) ": null );
            ((!$this->dateStart && $this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') <= :dateend ) ": null );
            ( $this->IdServiceCentre ? $queryCondition .= " AND c.IdServiceCentre = :centre" : null);
            
            $query = new Query(); 
            $query->select(['a.Id','a.Name','count(c.Id) Quantity']);
            $query->from(Type::tableName().' a');
            $query->innerJoin(State::tableName().' b', 'b.Id = a.IdState');
            $query->where([
                'b.Code' => Type::STATUS_ACTIVE,
                'a.KeyWord' => StringHelper::basename(Incident::class).'Interrupt',
            ]);
            $query->leftJoin(Securityincident::tableName().' c', "c.IdInterruptType = a.Id AND c.IdType IN (SELECT d.Id FROM ".Type::tableName()." d WHERE d.KeyWord ='". StringHelper::basename(Securityincident::class)."' ) ".$queryCondition, $queryParams);
            #$query->leftJoin(Type::tableName().' d', "d.Id = c.IdType AND d.KeyWord ='".StringHelper::basename(Securityincident::class)."'");
            $query->groupBy(['a.Id','a.Name']);
            $query->orderBy(['a.Id' => SORT_ASC]);
            $result = $query->all();
            $response = [];
            $dataset = [];
            $drilldown = ["series"=>[]];
            
            foreach ($result as $r){
                $params = array_merge([':id'=> $r["Id"]], $queryParams);
                $query_operations = new Query();
                $query_operations->select(['a.Id','a.Name','count(c.Id) Quantity']);
                $query_operations->from(Servicecentre::tableName().' a');
                $query_operations->innerJoin(State::tableName().' b', 'b.Id = a.IdState');
                $query_operations->innerJoin(Type::tableName().' e', 'e.Id = a.IdType');
                $query_operations->leftJoin(self::tableName().' c', 'c.IdServiceCentre = a.Id AND c.IdInterruptType = :id '.$queryCondition, $params);
                $query_operations->leftJoin(Type::tableName().' d', "d.Id = c.IdType AND d.KeyWord ='".StringHelper::basename(Securityincident::class)."'");
                $query_operations->where([
                    #'b.Code' => Servicecentres::STATE_ACTIVE,
                    'e.Code' => [Servicecentre::TYPE_DUISITE, Servicecentre::TYPE_DATACENTER],
                ]);
                $query_operations->groupBy(['a.Id','a.Name']);
                $query_operations->orderBy(['a.Id'=> SORT_ASC]);
                $details = $query_operations->all();
                
                $dataApp = [];
                foreach ($details as $det){
                    $dataApp[] = [$det['Name'], (int) $det['Quantity']];
                }
                
                $drilldown['series'][] = [
                    'name'=> $r['Name'],
                    'id'=> $r['Name'],
                    'colorByPoint'=>TRUE,
                    'data'=> $dataApp,
                ];
                $dataset[] = ['name'=>$r['Name'],'y'=>(int)$r['Quantity'],"drilldown"=>$r['Name']];
            }
            $response['dataset'] = $dataset;
            $response['drilldown'] = $drilldown;
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getTotalByMonth($criteria = []){
        try {
            $months = [];
            $response = [];
            $queryParams = [];
            $queryCondition = "";
            $n = 12;
            $dateStart = $this->dateStart ? \DateTime::createFromFormat('d-m-Y', $this->dateStart) : null;
            $dateEnd = $this->dateEnd ? \DateTime::createFromFormat('d-m-Y', $this->dateEnd) : null;
            
            ($this->Year ? $queryParams[':year'] = $this->Year: null);
            ($this->dateStart ? $queryParams[':datestart'] = $dateStart->format('Y-m-d'): null);
            ($this->dateEnd ? $queryParams[':dateend'] = $dateEnd->format('Y-m-d'): null);
            ($this->IdServiceCentre ? $queryParams[':centre'] = $this->IdServiceCentre: null);
            
            ($this->Year ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y') = :year )" : null);
            (($this->dateStart && $this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') BETWEEN :datestart AND :dateend ) ": null );
            (($this->dateStart && !$this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') >= :datestart ) ": null );
            ((!$this->dateStart && $this->dateEnd) ? $queryCondition .= " AND (date_format(c.IncidentDate,'%Y-%m-%d') <= :dateend ) ": null );
            ( $this->IdServiceCentre ? $queryCondition .= " AND c.IdServiceCentre = :centre" : null);
            
            for($i = 1; $i <= $n; $i++){
                $detParams = $queryParams;
                $detCondition = $queryCondition;
                $detParams[':month'] = $i;
                $detCondition .= " AND month(c.IncidentDate) = :month ";
                $query = new Query();
                $query->select(['a.Id', 'a.Name', 'getmonthname(:month) MonthName', 'count(c.Id) Quantity']);
                $query->from(Type::tableName().' a');
                $query->innerJoin(State::tableName().' b','b.Id = a.IdState');
                $query->leftJoin(self::tableName().' c', 'a.Id = c.IdType '.$detCondition, $detParams);
                $query->where([
                    'a.KeyWord' => StringHelper::basename(Securityincident::class),
                    'b.Code' => Type::STATUS_ACTIVE,
                ]);
                $query->groupBy(['a.Id', 'a.Name']);
                $query->orderBy(['a.Id' => SORT_ASC]);
                $details = $query->all();
                $j = $i - 1;
                foreach ($details as $det){
                    
                }
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
