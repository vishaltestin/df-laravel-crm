<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

// app/UserActivityLog.php
class UserActivityLog extends Model {
  protected $table = 'user_activity_logs';
  public $timestamps = true;
  protected $fillable = [
    'organization_id','user_id','entity_type','entity_id','action','description','meta_data',
    'ip_address','device','browser','os','created_at'
  ];
  protected $casts = ['meta_data' => 'array','created_at' => 'datetime'];
}
