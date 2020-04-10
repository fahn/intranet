<?php 

class Gender {
    const OPTIONS = array('MALE', 'FEMALE');

    /**
     * return all Genders
     *
     * @return array
     */
    public function getGenderArray(): array {
        return self::OPTIONS;
    }
}

?>