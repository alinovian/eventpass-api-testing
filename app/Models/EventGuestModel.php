<?php

namespace App\Models;

use CodeIgniter\Model;

class EventGuestModel extends Model
{
    protected $table            = 'eventguests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['eventID', 'guestID', 'categoryID', 'qrcode', 'status'];

    public function getTicketDetail($qrcode)
    {
        return $this->select('eventGuests.*, guests.fullName, guestCategories.name as category')
            ->join('guests', 'guests.id = eventGuests.guestID')
            ->join('guestCategories', 'guestCategories.id = eventGuests.categoryID')
            ->where('qrcode', $qrcode)
            ->first();
    }

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
