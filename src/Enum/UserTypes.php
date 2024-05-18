<?php

namespace App\Enum;

enum UserTypes: string
{
    case ETUDIANT = 'etudiant';
    case ENSEIGNANT = 'enseignant';
    case ADMIN = 'administrateur';

    
}
