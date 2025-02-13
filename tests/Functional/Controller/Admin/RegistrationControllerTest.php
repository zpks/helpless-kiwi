<?php

namespace Tests\Functional\Controller\Admin;

use App\Entity\Activity\Activity;
use App\Entity\Activity\Registration;
use App\Tests\AuthWebTestCase;
use App\Tests\Database\Activity\ActivityFixture;
use App\Tests\Database\Activity\PriceOptionFixture;
use App\Tests\Database\Activity\RegistrationFixture;
use App\Tests\Database\Security\LocalAccountFixture;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class RegistrationControllerTest.
 *
 * @covers \App\Controller\Admin\RegistrationController
 *
 * @author A-Daneel
 */
class RegistrationControllerTest extends AuthWebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    private $controller = '/admin/group/';

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseTool->loadFixtures([
            LocalAccountFixture::class,
            PriceOptionFixture::class,
            ActivityFixture::class,
            RegistrationFixture::class,
        ]);

        $this->login();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->em);
    }

    public function testNewActionGet(): void
    {
        // Arrange
        $activity = $this->em->getRepository(Activity::class)->findAll()[0];
        $id = $activity->getId();

        // Act
        $this->client->request('GET', "/admin/activity/register/new/{$id}");

        // Assert
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     *  @depends testNewActionGet
     */
    public function testNewActionPost(): void
    {
        // Arrange
        $activity = $this->em->getRepository(Activity::class)->findAll()[0];
        $originalCount = $activity->getRegistrations()->count();
        $id = $activity->getId();

        // Act
        $this->client->request('GET', "/admin/activity/register/new/{$id}");
        $this->client->submitForm('Toevoegen');

        // Assert
        $activity = $this->em->getRepository(Activity::class)->find($id);
        $newCount = $activity->getRegistrations()->count();
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSelectorTextContains('.container', 'gelukt');
        self::assertEquals(1, $newCount - $originalCount, "Registration count of activity didn't correctly change after POST request.");
    }

    public function testEditActionPost(): void
    {
        // Arrange
        $registration = $this->em->getRepository(Registration::class)->findAll()[0];
        $id = $registration->getId();
        $crawler = $this->client->request('GET', "/admin/activity/register/edit/{$id}");
        $comment = 'This is a test comment';

        // Act
        $form = $crawler->selectButton('Verander')->form();
        $form['registration_edit[comment]'] = $comment;
        $this->client->submit($form);

        // Assert
        $currentcomment = $this->em->getRepository(Registration::class)->find($id);
        $newcomment = $currentcomment->getComment();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertEquals($comment, $newcomment);
    }

    public function testDeleteActionGet(): void
    {
        // Arrange
        $registration = $this->em->getRepository(Registration::class)->findAll()[0];
        $id = $registration->getId();

        // Act
        $this->client->request('GET', "/admin/activity/register/delete/{$id}");

        // Assert
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     *  @depends testDeleteActionGet
     */
    public function testDeleteActionPost(): void
    {
        // Arrange
        $registration = $this->em->getRepository(Registration::class)->findAll()[0];
        $id = $registration->getId();
        self::assertEquals(null, $registration->getDeleteDate());

        // Act
        $crawler = $this->client->request('GET', "/admin/activity/register/delete/{$id}");
        $form = $crawler->selectButton('Ja, meld af')->form();
        $this->client->submit($form);

        // Assert
        $registration = $this->em->getRepository(Registration::class)->find($id);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertSelectorTextContains('.container', 'gelukt');
        self::assertNotNull($registration->getDeleteDate());
    }

    public function testReserveNewActionGet(): void
    {
        // Arrange
        $activity = $this->em->getRepository(Activity::class)->findAll()[0];
        $id = $activity->getId();

        // Act
        $this->client->request('GET', "/admin/activity/register/reserve/new/{$id}");

        // Assert
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @depends testReserveNewActionGet
     */
    public function testReserveNewActionPost(): void
    {
        // Arrange
        $activity = $this->em->getRepository(Activity::class)->findAll()[0];
        $originalCount = $activity->getRegistrations()->count();
        $id = $activity->getId();

        // Act
        $this->client->request('GET', "/admin/activity/register/reserve/new/{$id}");
        $this->client->submitForm('Toevoegen');

        // Assert
        $activity = $this->em->getRepository(Activity::class)->find($id);
        $newCount = $activity->getRegistrations()->count();
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertEquals(1, $newCount - $originalCount, "Registration count of activity didn't correctly change after POST request.");
        self::assertSelectorTextContains('.container', 'op de reservelijst');
    }

    public function testReserveMoveUpAction(): void
    {
        // Arrange
        $activity = $this->em->getRepository(Activity::class)->findAll()[0];
        $reserves = $this->em->getRepository(Registration::class)->findReserve($activity);
        $secondReserveId = $reserves[1]->getId();

        // Act
        $this->client->request('GET', "/admin/activity/register/reserve/move/{$secondReserveId}/up");

        // Assert
        $updatedReserves = $this->em->getRepository(Registration::class)->findReserve($activity);
        $updatedFirstReserveId = $updatedReserves[0]->getId();
        self::assertEquals($updatedFirstReserveId, $secondReserveId);
        self::assertSelectorTextContains('.container', 'naar boven verplaatst!');
    }

    public function testReserveMoveDownAction(): void
    {
        // Arrange
        $activity = $this->em->getRepository(Activity::class)->findAll()[0];
        $reserves = $this->em->getRepository(Registration::class)->findReserve($activity);
        $firstReserveId = $reserves[0]->getId();

        // Act
        $this->client->request('GET', "/admin/activity/register/reserve/move/{$firstReserveId}/down");

        // Assert
        $updatedReserves = $this->em->getRepository(Registration::class)->findReserve($activity);
        $updatedRegistrationId = $updatedReserves[1]->getId();
        self::assertEquals($updatedRegistrationId, $firstReserveId);
        self::assertSelectorTextContains('.container', 'naar beneden verplaatst!');
    }

    /**
     * @dataProvider noAccessProvider
     */
    public function testNoAccess(string $url): void
    {
        //arrange
        /** @var Activity $activity */
        $activity = $this->em->getRepository(Activity::class)->findAll()[0];

        /** @var Registration $registration */
        $registration = $activity->getRegistrations()[0];
        $id = $registration->getId();

        $reserve = $this->em->getRepository(Registration::class)->findReserve($activity)[0];
        $reserveId = $reserve->getId();

        $url = str_replace('id', strval($id), $url);
        $url = str_replace('rid', strval($reserveId), $url);

        //act
        $this->logout();
        $this->login([]);
        $this->client->request('GET', $url);

        //assert
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider noAccessProvider
     */
    public function testNullActivityNotAdmin(string $url): void
    {

        //arrange
        /** @var Registration $registration */
        $registration = $this->em->getRepository(Registration::class)->findOneBy(['activity' => null]);
        $id = $registration->getId();

        $url = str_replace('id', strval($id), $url);
        $url = str_replace('rid', strval($id), $url);

        //act
        $this->logout();
        $this->login([]);
        $this->client->request('GET', $url);

        //assert
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return iterable<array{string}>
     */
    public function noAccessProvider()
    {
        return [
            ['/admin/activity/register/edit/id'],
            ['/admin/activity/register/delete/id'],
            ['/admin/activity/register/reserve/new/id'],
            ['/admin/activity/register/reserve/move/rid/up'],
            ['/admin/activity/register/reserve/move/rid/down'],
        ];
    }
}
