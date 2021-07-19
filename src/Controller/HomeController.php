<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use App\Repository\TestRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\Extension\Core\DataMapper\RadioListMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    private $testRepo;

    public function __construct(
        TestRepository $testRepo
    ){
        $this->testRepo = $testRepo;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        $render = parent::renderDefault();

        $uuid_cookie = $request->cookies->get('uuid');
        $nick_cookie = $request->cookies->get('nick');

        $session = $request->getSession();

        $tests = $this->testRepo->findAll();
        $options = [];
        foreach ($tests as $test) {
            $options[$test->getTitle()] = $test->getId();
        }
        dump($tests);
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('nick', TextType::class, [
                'label' => "Ваше имя"
            ])
            ->add('test', ChoiceType::class,
                [
                    'label'=>false,
                    'choices' => $options,
                    'expanded' => true,

                ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $response = new Response();
            $data = $form->getData();
            $uuid = Uuid::uuid4();

            $session->set("uuid",  $uuid);
            $session->set("nick",  $data["nick"]);


            return $this->redirectToRoute('test', ['test'=>$data['test'], 'step'=>1]);
        }

        return $this->render('home.html.twig', [
            'form' => $form->createView()
        ]);

    }

}

