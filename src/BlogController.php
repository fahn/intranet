
<?php
namespace APP;
#use Symfony\Component\Routing\Annotation\Route;

class BlogController {

    public function __construct()
    {
        echo "1";
    }
    /**
     * @Route("/blog", name="blog_list")
     */
    public function list()
    {
        echo "2";
    }

    /**
     * @Route("/")
     *
     * @return void
     */
    public function test()
    {
        echo "1";
    }
}
