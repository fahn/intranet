<?php
namespace APP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController {

    public function __construct()
    {
    }


    /**
     * @Route("/blog", name="blog_list")
     */
    public function list()
    {
        echo "2";
    }

    /**
     * @Route("/das1")
     *
     * @return void
     */
    public function test()
    {
        echo "default";
    }

    /**
     * @Route("/foo2")
     *
     * @return void
     */
    public function foo()
    {
        return new Response("!!!", 200);
    }

    public function foo2()
    {
        return "2";
    }
}
