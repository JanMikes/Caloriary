<?php

use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Value\CaloricRecordId;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use League\FactoryMuffin\FactoryMuffin;
use Ramsey\Uuid\Uuid;

/** @var FactoryMuffin $fm */
$fm->define(User::class)->setMaker(function() {
	$user = (new \ReflectionClass(User::class))->newInstanceWithoutConstructor();

	$property = new \ReflectionProperty(User::class, 'emailAddress');
	$property->setAccessible(true);
	$property->setValue($user, EmailAddress::fromString('john@doe.com'));

	$property = new \ReflectionProperty(User::class, 'passwordHash');
	$property->setAccessible(true);
	$property->setValue($user, ClearTextPassword::fromString('123')->makeHash());

	return $user;
});

$fm->define(CaloricRecord::class)->setMaker(function() use ($fm) {
	$record = (new \ReflectionClass(CaloricRecord::class))->newInstanceWithoutConstructor();

	$property = new \ReflectionProperty(CaloricRecord::class, 'id');
	$property->setAccessible(true);
	$property->setValue($record, CaloricRecordId::fromString(Uuid::uuid4()->toString()));

	$property = new \ReflectionProperty(CaloricRecord::class, 'owner');
	$property->setAccessible(true);
	$property->setValue($record, $fm->instance(User::class));

	$property = new \ReflectionProperty(CaloricRecord::class, 'calories');
	$property->setAccessible(true);
	$property->setValue($record, Calories::fromInteger(1000));

	$property = new \ReflectionProperty(CaloricRecord::class, 'ateAt');
	$property->setAccessible(true);
	$property->setValue($record, new \DateTimeImmutable());

	$property = new \ReflectionProperty(CaloricRecord::class, 'text');
	$property->setAccessible(true);
	$property->setValue($record, MealDescription::fromString('Pudding'));

	return $record;
});
