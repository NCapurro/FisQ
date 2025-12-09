<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;





class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'lastname',
        'dni',
        'phone',
        'address',
        'department_id',
    ];

    //El rol se asigna manualmente para definir admins

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }


    // Relación: pertenece a un departamento
    public function department()
    {
        return $this->belongsTo(Department::class); 
    }

    //Tiene mesas asignadas
    public function mesas()
    {
        return $this->hasMany(Mesa::class);
    }

    //Tiene resultados que cargo
    public function results()
    {
        return $this->hasMany(Result::class);
    }


    // Mutador para el atributo 'name', mayus y minus
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::title($value),
            set: fn ($value) => Str::lower($value),
        );
    }

    //Mutador para el apellido
    protected function lastname(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::title($value),
            set: fn ($value) => Str::lower($value),
        );
    }

    //fullname
   protected function fullName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                // Usamos $attributes para obtener el dato crudo de la DB (minúsculas)
                // Formato Padrón: APELLIDO (Mayús), Nombre (Title)
                return Str::upper($attributes['lastname']) . ', ' . Str::title($attributes['name']);
            }
        );
    }



}






