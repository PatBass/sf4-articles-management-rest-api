<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var SerializerInterface
     */
    protected $jmsSerializer;

    /**
     * @var JWTEncoderInterface
     */
    protected $jwtEncoder;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     * @param ValidatorInterface $validator
     * @param SerializerInterface $jmsSerializer
     * @param JWTEncoderInterface $jwtEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(ValidatorInterface $validator, SerializerInterface $jmsSerializer, JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->validator = $validator;
        $this->jmsSerializer = $jmsSerializer;
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }


    /**
     * @Rest\Post(path = "login", name="userlogin")
     *
     */
    public function userLogin(Request $request, UserRepository $userRepository)
    {
        $data = json_decode($request->getContent(), true)?: [];

        $user = $userRepository->findOneBy(['email' => $data['email']]);


        $token = $this->jwtEncoder->encode(['email' => $data['email']]);

        $message = "You are successfully authenticated!";

        return $this->view(['message' => $message, 'token' => $token], Response::HTTP_OK);
    }

    /**
     * @Rest\Post(path = "user", name="user_create")
     * @Rest\View(StatusCode = 201)
     */
    public function createUser(Request $request): View
    {
        $validator = $this->validator;
        $jmsSerializer = $this->jmsSerializer;

        $data = $request->getContent();
        $user = $jmsSerializer->deserialize($data, 'App\Entity\User', 'json');

        $errors = $validator->validate($user);

        if (count($errors)) {
            return $this->view($errors, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->view($user, Response::HTTP_CREATED);
    }

    /**
     * @Rest\View(statusCode=200)
     * @Rest\Get(
     *     path = "user/{id}",
     *     name="user_show",
     *     requirements={"id"="\d+"}
     * )
     */
    public function showUser(User $user): View
    {
        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(path = "user", name="user_list")
     */
    public function userList(): View
    {
        $users = $this->userRepository->findAll();
        return $this->view($users, Response::HTTP_OK);
    }

    /**
     * @Rest\View(statusCode=200)
     * @Rest\Put(
     *     path = "user/{id}",
     *     name="user_edit",
     *     requirements={"id"="\d+"}
     * )
     * @ParamConverter("newUser", converter = "fos_rest.request_body")
     */
    public function editUser(Request $request, User $newUser, User $user): View
    {
        $errors = $this->validator->validate($newUser);

        if (count($errors)) {
            return $this->view($errors, Response::HTTP_BAD_REQUEST);
        }

        $user->setUsername($newUser->getUsername());
        $user->setEmail($newUser->getEmail());
        $user->setPassword($newUser->getPassword());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path = "user/{id}",
     *     name="user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View(statusCode=204)
     */
    public function delete(Request $request, User $user): View
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        $message = "The user has been successfully deleted!";
        return $this->view($message, Response::HTTP_OK);
    }
}
