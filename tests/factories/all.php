<?php

use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use League\FactoryMuffin\FactoryMuffin;

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
