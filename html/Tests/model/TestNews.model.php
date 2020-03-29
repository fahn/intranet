<?php 
declare(strict_types=1);

#namespace Tests\model\News;

use PHPUnit\Framework\TestCase;

class NewsTest extends TestCase
{
    private array $newsArr;
    private Object $newsObj;

    public function __construct()
    {
        $this->newsArr = array(
            'newsTitle'    => 'Meine Headline',
            'newsCategory' => 1,
            'newsText'     => 'das ist mein 1. Test',
        );
        $this->newsObj = new \News($this->newsArr);
    }

    public function testNewsTitle(): void
    {
        $this->assertEquals($this->newsArr['newsTitle'], $this->newsObj->getNewsTitle());
    }

    public function testNewsText(): void
    {
        $this->assertEquals($this->newsArr['newsText'], $this->newsObj->getNewsText());
    }

    public function testNewsCategoryId(): void
    {
        $this->assertEquals($this->newsArr['newsCategory'], $this->newsObj->getNewsCategory());
    }



}

?>