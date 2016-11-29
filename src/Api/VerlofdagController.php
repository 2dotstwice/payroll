<?php
namespace TwoDotsTwice\Payroll\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TwoDotsTwice\Payroll\UserService;
use TwoDotsTwice\Payroll\VerlofdagService;

class VerlofdagController
{

    protected $userService;
    protected $verlofdagService;

    public function __construct(UserService $userService, VerlofdagService $verlofdagService)
    {
        $this->userService = $userService;
        $this->verlofdagService = $verlofdagService;
    }
    public function getAll()
    {
        return new JsonResponse($this->verlofdagService->getAll());
    }
}
