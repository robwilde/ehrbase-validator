<?php

namespace App\Services\Archie\RMInfo;

interface ModelInfoLookup
{
    public function getClass($var1);

    public function getClassToBeCreated($var1);

    public function getRmTypeNameToClassMap();

    public function getTypeInfo($var1);

    public function getField($var1, $var2);

    public function getAttributeInfo($var1, $var2);

    public function getAllTypes();

    public function getNamingStrategy();

    public function convertToConstraintObject($var1, $var2);

    public function convertConstrainedPrimitiveToRMObject($var1);

    public function processCreatedObject($var1, $var2);

    public function getArchetypeNodeIdFromRMObject($var1);

    public function getArchetypeIdFromArchetypedRmObject($var1);

    public function getNameFromRMObject($var1);

    public function clone($var1);

    public function pathHasBeenUpdated($var1, $var2, $var3, $var4);

    public function validatePrimitiveType($var1, $var2, $var3);

    public function getId();

    // Note: This is an adaptation. PHP interfaces don't have default methods.
    //       You'd need to implement this in each class that uses this interface.
    public function referenceModelPropMultiplicity($rmTypeName, $rmAttributeNameOrPath);

    // Note: This is an adaptation. PHP interfaces don't have default methods.
    //       You'd need to implement this in each class that uses this interface.
    public function rmTypesConformant($childType, $parentType);
}
