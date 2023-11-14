<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    
    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
        
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules      = [	
        
    'id' => 'permit_empty|is_natural_no_zero', // <-- ESSA LINHA DEVE SER ADICIONADA
	
	// as existentes
	'nome'         => 'required|min_length[3]|max_length[125]',
	'email'        => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]', // Não pode ter espaços - Também verifica se o e-mail tem no banco de dados.
	'password'     => 'required|min_length[6]',
	'password_confirmation' => 'required_with[password]|matches[password]'
];
    protected $validationMessages   = [
        'nome'            =>[
            'required' => 'Eu preciso do seu nome',
            'min_length' => 'Nome Precisa ter 3 Caractéres',
            'max_length' => 'Nome NÃO pode ser maior que 125 caractéres',
        ],
        'email'            =>[
            'required' => 'Eu preciso do seu e-mail',
            'max_length' => 'Aceito email até 230 caractéres',
            'is_unique' => 'Esse e-mail já está em uso.'
        ],
        'password_confirmation'            =>[
            'required_with' => 'Por favor confirme a sua senha.',
            'matches' => 'As senhas tem que ser iguais.',
            
        ],
    ];
    

    // Callbacks

    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
           
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);

            // Removemos dos dados a serem salvos
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
           
        }

        return $data;
    }
}
