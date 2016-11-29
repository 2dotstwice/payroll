<?php
namespace TwoDotsTwice\Payroll;

class VerlofdagService extends BaseService
{
    const TABLE_INCREASING = 'verlofdagen_optellen';
    const TABLE_DECREASTING = 'verlofdagen_aftellen';

    public function getIncreasingTypes()
    {
        return $this->db->fetchAll('SELECT * FROM '.self::TABLE_INCREASING);
    }
    public function getDecreasingTypes()
    {
        return $this->db->fetchAll('SELECT * FROM '.self::TABLE_DECREASTING);
    }

    public function getAll()
    {
        return [
            'increasing' => $this->getIncreasingTypes(),
            'decreasing' => $this->getDecreasingTypes(),
        ];
    }

    public function userTakesIncreasing(PayrollUserInterface $user, $amount)
    {
    }
}
