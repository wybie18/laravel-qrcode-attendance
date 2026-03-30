<?php

use App\Models\AttendanceLog;
use App\Models\Company;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Position;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('defines fillable attributes for domain models', function () {
    expect((new Company())->getFillable())->toBe(['name']);
    expect((new Position())->getFillable())->toBe(['name']);
    expect((new Office())->getFillable())->toBe(['name', 'category']);

    expect((new Personnel())->getFillable())->toBe([
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone_number',
        'qr_code',
        'company_id',
        'position_id',
    ]);

    expect((new AttendanceLog())->getFillable())->toBe(['log_date', 'time_in', 'time_out']);
});

it('defines attendance log casts', function () {
    $casts = (new AttendanceLog())->getCasts();

    expect($casts)->toMatchArray([
        'log_date' => 'date',
        'time_in' => 'datetime:H:i:s',
        'time_out' => 'datetime:H:i:s',
    ]);
});

it('defines expected relationship methods and return types', function () {
    $companyRelationMethod = new ReflectionMethod(Company::class, 'personnels');
    expect($companyRelationMethod->hasReturnType())->toBeTrue();
    expect($companyRelationMethod->getReturnType()?->getName())->toBe(HasMany::class);

    $positionRelationMethod = new ReflectionMethod(Position::class, 'personnels');
    expect($positionRelationMethod->hasReturnType())->toBeTrue();
    expect($positionRelationMethod->getReturnType()?->getName())->toBe(HasMany::class);

    $personnelCompanyMethod = new ReflectionMethod(Personnel::class, 'company');
    expect($personnelCompanyMethod->hasReturnType())->toBeTrue();
    expect($personnelCompanyMethod->getReturnType()?->getName())->toBe(BelongsTo::class);

    $personnelPositionMethod = new ReflectionMethod(Personnel::class, 'position');
    expect($personnelPositionMethod->hasReturnType())->toBeTrue();
    expect($personnelPositionMethod->getReturnType()?->getName())->toBe(BelongsTo::class);
});