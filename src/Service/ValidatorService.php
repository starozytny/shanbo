<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate($object)
    {
        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {

            $errs = [];
            foreach ($errors as $error) {
                $errs[] = [
                    'name' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }

            return $errs;
        }

        return true;
    }

    public function validateCustom($data)
    {
        $errors = [];
        foreach($data as $elem){
            $validate = $this->switchCase($elem);
            if($validate != 1){
                array_push($errors, [
                    'name' => $elem['name'],
                    'message' => $validate
                ]);
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        return true;
    }

    private function switchCase($elem)
    {
        return match ($elem['type']) {
            'array' => $this->validateArray($elem['value']),
            'uniqueLength' => $this->validateUniqueLength($elem['value'], $elem['size']),
            'length' => $this->validateLength($elem['value'], $elem['min'], $elem['max']),
            default => $this->validateText($elem['value']),
        };
    }

    private function validateArray($value)
    {
        if(count($value) <= 0){
            return 'Ce champ doit être renseigné.';
        }

        return 1;
    }

    private function validateUniqueLength($value, $size)
    {
        if(strlen((string) $value) !== $size){
            return 'Ce champ doit contenir ' . $size . ' caractères.';
        }

        return 1;
    }

    private function validateLength($value, $min, $max)
    {
        if(strlen((string) $value) < $min || strlen((string) $value) > $max){
            return 'Ce champ doit contenir entre ' . ($min + 1) . ' et ' . $max . ' caractères.';
        }

        return 1;
    }

    private function validateText($value)
    {
        if($value == ""){
            return 'Ce champ doit être renseigné';
        }

        return 1;
    }
}