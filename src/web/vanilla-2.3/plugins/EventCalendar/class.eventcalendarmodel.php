<?php defined('APPLICATION') or die;

/**
 * Model to get event calendar information.
 *
 * @package EventCalendar
 * @author Robin Jurinka
 * @license MIT
 */
class EventCalendarModel extends VanillaModel {
    /**
     * Get information for a specific period.
     *
     * @param string $begin String symbolizing a start date.
     * @param string $end String symbolizing an end date.
     * @param integer $offset Offset of the events to fetch.
     * @param integer $limit Limit of the events to fetch (0 for no limit).
     * @return void.
     * @package EventCalendar
     * @since 0.1
     */ 
    public function get($begin = '0000-00-00', $end = '0000-00-00', $offset = 0, $limit = 0) {
        // Validate parameters, set today as default
        $beginDate = strtotime($begin);
        if ($beginDate <= 0) {
            $beginDate = date('Y-m-d');
        } else {
            $beginDate = date('Y-m-d', $beginDate);
        }

        $endDate = strtotime($end);
        if ($endDate <= 0) {
            $endDate = date('Y-m-d');
        } else {
            $endDate = date('Y-m-d', $endDate);
        }
        
        $offset = (int)$offset;
        $limit = (int)$limit;
        if ($limit == 0) {
            $limit = '';
        }

        $sql = Gdn::sql();
        $sql->select('d.DiscussionID, d.Name, d.Body, d.Format, d.DateInserted, d.EventCalendarDate')
            ->select('d.InsertUserID', '', 'UserID')
            ->from('Discussion d')
            ->where('d.EventCalendarDate >=', $beginDate)
            ->where('d.EventCalendarDate <=', $endDate)
            ->orderBy('d.EventCalendarDate')
            ->limit($limit, $offset);

        // add permission restrictions if necessary 
        $perms = DiscussionModel::categoryPermissions();
        if ($perms !== true) {
            $sql->whereIn('d.CategoryID', $perms);
        }

        // return $Sql->GetSelect();
        return $sql->get()->resultArray();
   }
}
