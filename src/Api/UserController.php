<?php
namespace TwoDotsTwice\Payroll\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TwoDotsTwice\Payroll\UserService;

class UserController
{

    protected $userService;
    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }
    public function getOne($id)
    {
        return new JsonResponse($this->userService->getOne($id));
    }
    public function getAll()
    {
        return new JsonResponse($this->userService->getAll());
    }
    public function save(Request $request)
    {
        $user = $this->getDataFromRequest($request);
        return new JsonResponse(array('id' => $this->userService->save($user)));
    }
    public function update($id, Request $request)
    {
        $user = $this->getDataFromRequest($request);
        $this->userService->update($id, $user);
        return new JsonResponse($user);
    }
    public function delete($id)
    {
        return new JsonResponse($this->userService->delete($id));
    }
    public function getDataFromRequest(Request $request)
    {
        return $user = [
            "user" => $request->request->get("user"),
        ];
    }
}
