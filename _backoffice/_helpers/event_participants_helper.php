<?php

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

$where = "WHERE ep.event_id = $event_id";

if($status == 'invited'){$where .= " AND ep.status = 1";}

if($status == 'confirmed'){$where .= " AND ep.status = 2";}

if($status == 'checked'){$where .= " AND ep.status = 3";}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;

$sql_total = "SELECT COUNT(*) as total FROM event_participants ep JOIN participants p ON p.id = ep.participant_id $where";

$total_result = select_sql_unic($sql_total);

$total_participants = $total_result ? (int)$total_result['total'] : 0;
$total_pages = ceil($total_participants / $limit);
if($total_pages < 1){
  $total_pages = 1;
}

if($page > $total_pages){
  $page = $total_pages;
}

if($page < 1){
  $page = 1;
}

$offset = ($page - 1) * $limit;

$sql = "SELECT ep.id as event_participant_id, p.title, p.first_name, p.last_name, p.company, p.email, ep.status FROM event_participants ep JOIN participants p ON p.id = ep.participant_id $where LIMIT $limit OFFSET $offset";

$participants = select_sql($sql);

$sql_event = "SELECT id, name FROM events WHERE id = $event_id";
$event = select_sql_unic($sql_event);

?>