<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'condition',
        'location',
    ];

    public function maintenanceReports()
    {
        return $this->hasMany(MaintenanceReport::class);
    }
}
