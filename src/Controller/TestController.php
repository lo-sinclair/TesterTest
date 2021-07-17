<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Test;
use App\Form\AnswerType;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\TestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class TestController extends BaseController
{
    private $questionRepo;
    private $answerRepo;

    public function __construct(
        TestRepository $testRepo,
        QuestionRepository $questionRepo,
        AnswerRepository $answerRepo
    )
    {
        $this->questionRepo = $questionRepo;
        $this->answerRepo = $answerRepo;
    }


    /**
     * @Route("/test/{test}/{step}", name="test")
     * @param Test $test
     * @param int $step
     * @param Request $request
     * @return Response
     */
    public function test(Test $test, int $step, Request $request): Response
    {
        $render = parent::renderDefault();

        $session = $request->getSession();
        $uuid = $session->get("uuid");
        $nick = $session->get("nick");

        $question = $this->questionRepo->findOneBy(['test'=>$test, 'step'=>$step]);

        $answer = new Answer();
        $answer->setNick($nick);
        $answer->setUuid($uuid);
        $answer->setCreatedAt(new \DateTime());
        $answer->setQuestion($question);
        $answer->setResult(0);

        $form = $this->createForm(AnswerType::class, $answer, array('question'=>$question ) );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer = $form->getData();
            $uanswer = $answer->getAnswer();
            $result = 1;
            foreach( $question->getTrueVariants() as $variant) {
               if(!in_array($variant, $uanswer)) {
                   $result = 0;
                   break;
               }
            }
            $answer->setResult($result);

            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();

            $step++;

            if($step>3) {
                return $this->redirectToRoute('result', ['uuid'=>$uuid]);
            }

            return $this->redirectToRoute('test', ['test'=>$test->getId(), 'step'=>$step, $request]);
        }

        $render['question'] = $question;
        $render['form'] = $form->createView();
        return $this->render('test.html.twig', $render);
    }

    /**
     * @Route("/result/{uuid}", name="result")
     * @param string $uuid
     * @param Request $request
     * @return Response
     */
    public function result(string $uuid, Request $request): Response
    {
        $render = parent::renderDefault();

        $session = $request->getSession();
        $answerss = $session->get('answers');dump($answerss);


        $answers = $this->answerRepo->findBy(['uuid'=>$uuid]);

        //рендер массив, предоставляющий данные для шаблона
        //будет вынесен в отдельный класс
        $answersData = [];
        foreach ($answers as $answer) {
            $answerDat = [];
            $question =$answer->getQuestion();

            $answerDat['question'] = $question->getQuestion();
            $answerDat['result'] = $answer->getResult();
            $answerDat['answer'] = implode(", ", $answer->getAnswer()) ;
            $answerDat['trueVariants'] = implode(", ", $question->getTrueVariants());

            $answersData[$question->getStep()] = $answerDat;
        }
        $render['answersData'] = $answersData;
        return $this->render('result.html.twig', $render);
    }

}
