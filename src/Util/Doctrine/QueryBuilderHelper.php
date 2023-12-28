<?php

namespace App\Util\Doctrine;

use App\Util\Rsql\RSQLHelper;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Types\UlidType;

abstract class QueryBuilderHelper
{

    private static ?RSQLHelper $resqHelper = null;



    public static function enableQueryCache(QueryBuilder $qb, string $region = 'default', string $cacheMode = 'NONSTRICT_READ_WRITE' /*'READ_WRITE'*/): void
    {
        $qb
            ->setCacheMode($cacheMode)
            ->setCacheRegion($region)
            ->setCacheable(true);
    }

    public static function applyCriteria(QueryBuilder $qb, array|string|object|null $criteria, string $root): void
    {


        // if (is_object($criteria)) {


        //     if ($criteria instanceof ShipmentOrderAwareMessageInterface) {

        //         if ($orderId = $criteria->getShipmentOrderId()) {
        //             // $orderJoin = 'JOIN_shipment_order_id'. uniqid();
        //             $orderParam = 'PARAM_shipment_order_id' . uniqid();
        //             $qb
        //                 // ->innerJoin("{$root}.shipmentOrder",$orderJoin)
        //                 // ->andWhere("{$orderJoin}.id = :{$orderParam}")
        //                 ->andWhere("{$root}.shipmentOrder = :{$orderParam}")
        //                 ->setParameter($orderParam, $orderId, UlidType::NAME);;
        //         }
        //     }




        //     if (($criteria instanceof CollectionSearchQuery) || ($criteria instanceof CollectionCountQuery))
        //         $criteria = $criteria->getFilter();
        // }


        if ($criteria) {
            if (is_array($criteria)) {
                foreach ($criteria as $field => $value) {
                    $qb->andWhere($qb->expr()->eq("{$root}.{$field}", ":para_{$field}"));
                    // $parameters['para_' . $field] = $value;
                    $qb->setParameter('para_' . $field, $value);
                }
            } elseif (is_string($criteria)) {
                //Use RSQL Parse to conver the query to database critera
                static::getRsqlHelper()->applyFilter($qb, $root, $criteria);
            }
            // $qb->andWhere($qb->expr()->like('{$root}.name', '*'));
        }
    }



    private static function getRsqlHelper(): RSQLHelper
    {
        return /*self::$resqHelper ??=*/ new RSQLHelper();
    }
}
