<?php

namespace App\Models;

use CodeIgniter\Model;

class CampaignModel extends Model
{
    protected $table = 'campaigns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'id', 'name', 'slug', 'objective', 'description', 'history', 'structure',
        'status', 'config_geoloc', 'config_geoloc_mode',
        'offer_type', 'offer_title', 'offer_body', 'offer_image',
        'offer_link_url', 'offer_link_text', 'offer_cta_text',
        'og_title', 'og_description', 'og_image',
        'contact_name', 'contact_avatar', 'chat_messages', 'success_message',
        'success_title', 'email_subject', 'email_body', 'owner_message',
        'created_at', 'updated_at', 'expires_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'id'   => 'permit_empty|alpha_dash|max_length[36]',
        'name' => 'required|min_length[3]|max_length[255]',
        'slug' => 'required|alpha_dash|min_length[3]|max_length[100]|is_unique[campaigns.slug,id,{id}]',
    ];

    protected $validationMessages = [
        'name' => ['required' => 'O nome da campanha é obrigatório.'],
        'slug' => [
            'required' => 'O slug é obrigatório.',
            'is_unique' => 'Este slug já está em uso.',
        ],
    ];

    protected array $casts = [
        'config_geoloc' => 'boolean',
        'chat_messages' => 'json-array',
        'history' => 'json-array',
        'structure' => 'json-array',
    ];

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    public function getActive(): array
    {
        return $this->where('status', 'active')->findAll();
    }
}
