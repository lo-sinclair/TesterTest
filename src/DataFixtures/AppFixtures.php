<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Test;
use App\Repository\TestRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $testRepo;
    public function __construct(TestRepository $testRepo) {
        $this->testRepo = $testRepo;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->createTests($manager);
        $this->createQuestions($manager);
    }

    public function createQuestions(ObjectManager $manager) {

        $test = $this->testRepo->findAll()[0];

        $question = new Question();
        $question->setQuestion('На каком фреймворке написан этот сайт?');
        $question->setType('single');
        $question->setVariants([
            'Symfony'=>'symfony',
            'Laravel'=>'laravel',
            'jQuery'=>'jquery',
            'JavaScript'=>'javascript',
            'PHP'=>'php',
            'Python'=>'python',
            'HTML'=>'html']);
        $question->setTrueVariants(['symfony']);

        $question->setTest($test);
        $question->setStep(1);
        $manager->persist($question);

        $question = new Question();
        $question->setQuestion('Какие языки программирования из перечисленных используются для создания сайтов?');
        $question->setType('multiple');
        $question->setVariants([
            'Symfony'=>'symfony',
            'Laravel'=>'laravel',
            'jQuery'=>'jquery',
            'JavaScript'=>'javascript',
            'PHP'=>'php',
            'Python'=>'python',
            'HTML'=>'html']);
        $question->setTrueVariants(['php', 'javascript', 'python']);
        $question->setTest($test);
        $question->setStep(2);
        $manager->persist($question);


        $question = new Question();
        $question->setQuestion('Продолжите фразу: "Как здорово, что все мы здесь сегодня..."');
        $question->setType('text');
        $question->setTrueVariants(['собрались']);
        $question->setTest($test);
        $question->setStep(3);
        $manager->persist($question);
        $manager->flush();
    }

    public function createTests(ObjectManager $manager) {
        $test = new Test();
        $test->setTitle("Тест на эрудицию");
        $manager->persist($test);
        $manager->flush();
    }
}

