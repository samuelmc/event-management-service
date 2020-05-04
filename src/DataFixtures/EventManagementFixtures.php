<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Event;
use App\Entity\User;
use App\Service\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\MigratingPasswordEncoder;
use Symfony\Component\Security\Core\Security;

class EventManagementFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $passwordEncoder = new MigratingPasswordEncoder(new SodiumPasswordEncoder());
        $slugger = new Slugger();

        $superUser = new User();
        $manager->persist($superUser
            ->setUsername('Super Admin')
            ->setEmail('superadmin@event-management-service')
            ->setActive(true)
            ->setPassword($passwordEncoder->encodePassword('Super Admin', ''))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN','ROLE_SUPER_ADMIN'])
        );

        $adminUser = new User();
        $manager->persist($adminUser
            ->setUsername('Admin')
            ->setEmail('admin@event-management-service')
            ->setActive(true)
            ->setPassword($passwordEncoder->encodePassword('Admin', ''))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
        );

        $regularUser = new User();
        $manager->persist($regularUser
            ->setUsername('User')
            ->setEmail('user@event-management-service')
            ->setActive(true)
            ->setPassword($passwordEncoder->encodePassword('User', ''))
            ->setRoles(['ROLE_USER'])
        );

        $regularUserBis = new User();
        $manager->persist($regularUserBis
            ->setUsername('User Bis')
            ->setEmail('user-bis@event-management-service')
            ->setActive(true)
            ->setPassword($passwordEncoder->encodePassword('User Bis', ''))
            ->setRoles(['ROLE_USER'])
        );

        $users = [$superUser, $adminUser, $regularUser, $regularUserBis];

        $cityNames = ['Leuven','Brussels','Aarschot','Gent','Brugge','Antwerpen','Hasselt','Liege','Louvain-La-Neuve','Namur','Charleroi','Tienen','Diest','Mechelen'];
        $cities = [];
        foreach ($cityNames as $name) {
            $city = new City();
            $manager->persist($city
                ->setName($name)
                ->setActive(true)
            );
            $manager->persist($slugger->sluggifyEntity($manager, $city, 'name'));
            $cities[] = $city;
        }

        $events = [
            'Sed eu ex venenatis, lacinia elit vel, pulvinar mauris.' => 'Mauris tristique, metus non venenatis lacinia, purus odio faucibus justo, in hendrerit ipsum urna vitae justo. Quisque vitae risus ut turpis mollis malesuada. Donec imperdiet, turpis a sollicitudin aliquet, lorem eros rutrum dui, vitae pulvinar diam quam non diam. Aliquam molestie ligula nibh, eget laoreet ligula blandit eu. Morbi tincidunt eros ultricies magna hendrerit, non volutpat ipsum viverra. Nunc dictum at turpis eget ornare. Vivamus in lacus elit. Fusce tristique luctus neque, non porta lectus lacinia sed. Nullam auctor nisl quis odio tincidunt cursus. ',
            'Curabitur dignissim ex ac felis convallis posuere.' => 'Fusce vel bibendum ex. Etiam tempor, lectus a posuere efficitur, nunc lacus condimentum mauris, vel luctus ligula leo in metus. Nullam feugiat augue sit amet dapibus luctus. Mauris mattis turpis id semper malesuada. Donec nec nisi tincidunt arcu viverra luctus vitae eget magna. Aliquam mattis neque et lorem elementum, interdum vehicula nisi ultrices. Quisque lobortis consequat consectetur. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Maecenas elit risus, congue nec urna eget, congue pretium nulla. Nullam gravida quis ligula id fringilla. Nullam gravida egestas molestie. Etiam molestie vulputate enim, a pulvinar mauris pharetra eget. Aliquam sollicitudin blandit enim, eget efficitur risus tempus id. Pellentesque blandit rutrum urna condimentum dignissim. ',
            'Donec pretium dolor id erat interdum fringilla.' => 'Phasellus facilisis dignissim lacus, non bibendum justo porta ac. Duis suscipit condimentum venenatis. Integer eget justo vitae massa sagittis mattis ut in mi. Vivamus posuere auctor eros, et dignissim ipsum tincidunt a. Donec commodo semper felis a scelerisque. Nulla facilisi. Proin convallis est vel eleifend luctus. Mauris quis eros dapibus, ultrices nunc in, scelerisque sapien. Sed iaculis ex purus, ac egestas metus dignissim vel. Maecenas vel congue sapien. Etiam id diam ac erat accumsan volutpat. Donec tristique, felis a pulvinar sollicitudin, lorem enim egestas massa, eu facilisis leo velit vel quam. Proin feugiat massa sed varius scelerisque. Vestibulum tortor felis, tempor et eleifend quis, porttitor eget dolor. Nulla mattis, massa eget condimentum tristique, arcu felis lacinia metus, fermentum consectetur nisi erat eget tortor. ',
            'Pellentesque vel eros cursus, egestas mi id, vestibulum libero.' => 'Sed et varius nisl. Praesent libero lorem, iaculis vitae eleifend at, aliquam et neque. Duis nec ultricies est. Integer ac magna non massa fermentum viverra vel in massa. Mauris vehicula dui et enim placerat molestie. In hac habitasse platea dictumst. Vivamus libero leo, facilisis nec finibus non, feugiat faucibus urna. Proin ornare aliquam facilisis. Pellentesque non nibh vitae est pretium sodales. Integer eleifend mi quis purus ullamcorper, ac ultricies libero vulputate. Ut vitae tellus eget leo vestibulum ullamcorper sed sed magna. In rutrum mi ut placerat tempor. ',
            'Morbi eleifend nisl id elementum ornare.' => 'Quisque eget mi placerat, varius leo nec, egestas nisi. Mauris blandit mauris leo, eu semper magna lacinia sit amet. Quisque a ipsum at purus ultrices feugiat. Duis placerat dui et est congue, et cursus nisi sollicitudin. Pellentesque ultrices purus ac arcu vestibulum, id tincidunt nunc ultrices. Nunc tincidunt mauris ut scelerisque vulputate. Pellentesque in venenatis velit. ',
            'Quisque tristique metus quis ligula blandit tincidunt.' => 'Nullam fringilla luctus massa, id feugiat urna laoreet id. Ut tempor sodales mi a egestas. Nam sit amet tortor blandit, interdum elit eu, placerat magna. Ut id odio egestas, varius magna eget, porttitor dui. Vivamus in mauris sed velit viverra luctus commodo sed lectus. Phasellus volutpat tortor ut magna sagittis tempor. Nunc viverra metus et maximus elementum. Duis sed erat consequat, molestie erat a, bibendum erat. Nam eget risus massa. Suspendisse scelerisque condimentum sapien eu laoreet. Donec porttitor nulla eu viverra pulvinar. Nunc a maximus eros, non semper ligula. Duis nec dui et mi vehicula pellentesque a sed nibh. Praesent id mauris sed risus convallis congue. ',
            'Ut euismod justo eu nibh accumsan convallis.' => 'Vivamus in ex et libero vestibulum gravida a a enim. Nunc ut dapibus nisl. Nam condimentum neque ligula, id tristique ante facilisis at. Maecenas aliquam felis in condimentum congue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc bibendum tellus quis nisl laoreet eleifend. Donec nec posuere nulla. Suspendisse eleifend sed arcu ut aliquam. Mauris arcu augue, ornare ut luctus in, blandit et tellus. Sed faucibus mauris ut semper rutrum. Nullam quis arcu id elit fringilla molestie. Nunc mauris turpis, porttitor non turpis et, dapibus finibus lectus. ',
            'Vestibulum fermentum nunc sed commodo pulvinar.' => 'Donec sit amet quam neque. Morbi in ultricies nunc. Sed dapibus cursus ultricies. In hac habitasse platea dictumst. Cras eget urna quis ipsum imperdiet tristique in fermentum lorem. Cras erat orci, lacinia pellentesque tincidunt at, mattis quis risus. Suspendisse potenti. Quisque ut tellus sed felis tincidunt suscipit a at diam. Nulla et velit eu ex bibendum eleifend. Proin in ultricies augue. Fusce massa urna, sollicitudin a bibendum vitae, vehicula sed nulla. ',
            'Etiam sed nunc imperdiet, placerat magna vestibulum, vestibulum velit.' => 'Fusce et diam non mi molestie tristique eu quis ex. Nulla dapibus vulputate magna, ac sagittis sapien faucibus sit amet. Nulla ut efficitur eros. Fusce at eros quis odio sagittis pretium. Praesent id nisi urna. Vivamus pulvinar auctor pharetra. Donec elementum egestas tellus, at cursus velit. Suspendisse eget quam at velit aliquet elementum. Nunc pellentesque ac felis ac finibus. Vivamus tempus nisl vel risus luctus, non ornare erat interdum. Cras non ante elit. Quisque imperdiet felis erat, nec fermentum nibh consequat sed. Suspendisse lacus orci, rutrum sit amet viverra eu, ullamcorper sit amet ex. Cras semper nulla nec urna bibendum, sit amet dictum risus vestibulum. Sed blandit volutpat turpis eu venenatis. Morbi dapibus, tellus sed sagittis tempus, elit lacus blandit massa, a vehicula tortor purus vitae metus. ',
            'Morbi porttitor sapien eget erat malesuada vehicula.' => 'Mauris ultrices nibh elit, vitae ultricies augue feugiat et. Morbi rutrum tellus lectus, ac consequat risus tristique ac. Sed sit amet lacinia orci. Nullam pharetra, est a pharetra suscipit, nunc tortor condimentum tellus, maximus hendrerit velit est vel tellus. Ut et purus vel lectus scelerisque ultricies ullamcorper in nisl. Fusce vel commodo est. Sed congue dignissim faucibus. In dolor ex, dignissim vel fringilla vel, lacinia vitae purus. Nunc erat nulla, venenatis in auctor et, iaculis sit amet arcu. ',
            'Mauris viverra nisi et luctus dignissim.' => 'Etiam aliquet mi massa. Suspendisse aliquet neque at dignissim pellentesque. Nullam cursus vestibulum finibus. Quisque sit amet interdum enim, a varius arcu. Nunc eleifend lacus a posuere bibendum. Phasellus eu orci tortor. Donec sed libero eu purus lacinia lobortis. Cras mollis leo odio, vel molestie nibh ullamcorper eu. Nullam pretium, ex ut tincidunt tempus, neque lorem laoreet lorem, a porttitor urna lectus eu felis. Aliquam euismod facilisis est. Sed diam purus, hendrerit vel quam eget, ultricies hendrerit lectus. Donec a dui quis quam malesuada vestibulum. ',
            'Nulla venenatis arcu id tincidunt mollis.' => 'Nam id sodales lectus. Etiam posuere felis nisl, ac sagittis nisi blandit et. Ut scelerisque purus eget enim finibus interdum. Nunc semper ornare ipsum non sollicitudin. Nulla suscipit nisi odio, facilisis pulvinar orci ornare vitae. Nulla facilisi. Nullam euismod felis non tellus convallis pulvinar. Vestibulum vestibulum magna nec commodo rutrum. Sed sed est a ex vehicula sagittis. Pellentesque viverra lacinia erat id scelerisque. Aenean nec nunc semper velit accumsan iaculis ut in leo. Proin ligula lorem, mattis eget iaculis ac, euismod facilisis augue. ',
            'Phasellus consectetur justo et ex semper, quis consequat metus varius.' => 'Nullam id velit id sem auctor cursus dapibus id augue. Mauris gravida odio diam, non commodo metus bibendum cursus. Phasellus tempor vel felis non lobortis. Ut nec libero eget ante imperdiet porta eget quis metus. Nam pulvinar scelerisque imperdiet. Phasellus tempus magna faucibus dignissim hendrerit. Nam vel iaculis erat, vel bibendum metus. Donec malesuada lectus ut consectetur fermentum. Etiam ac magna dignissim dui euismod auctor. ',
            'Vestibulum dignissim lectus non dignissim auctor.' => 'Mauris metus erat, fringilla vel ligula id, convallis tempor mauris. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean suscipit blandit eleifend. In in neque sed arcu rutrum porttitor. Aenean vestibulum sed purus at dignissim. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam hendrerit ligula leo, eu consequat nulla faucibus a. Morbi placerat neque justo, et ullamcorper sem rutrum venenatis. Morbi sapien orci, elementum id lacinia eget, finibus a ex. Integer ac condimentum mi, fermentum varius sem. Maecenas ante risus, fermentum nec maximus sit amet, fringilla eget felis. ',
            'Etiam a dolor et lacus lacinia euismod.' => 'Etiam hendrerit iaculis augue, non porta nisi volutpat sed. Aliquam lectus nulla, dapibus id sagittis placerat, gravida at urna. Sed lacinia mi eu sem ornare, eget placerat dui pellentesque. Nunc vitae condimentum nisl. In vulputate euismod magna, eget pulvinar magna iaculis id. Quisque quis tellus justo. Vivamus nisi sapien, mattis ac posuere eu, bibendum a neque. Sed eu metus sed lorem hendrerit euismod. Nunc commodo at nunc et rutrum. Quisque augue urna, auctor et tristique quis, finibus a lectus. Suspendisse auctor tristique quam, imperdiet aliquet lectus cursus id. Pellentesque ut pellentesque nulla. Maecenas accumsan sollicitudin nisi, congue aliquet lorem congue eu. Mauris mattis orci condimentum, aliquet elit sit amet, eleifend libero. Etiam ultricies non massa non bibendum. ',
            'Mauris vitae odio vulputate, dapibus justo in, auctor sapien.' => 'Quisque eleifend nisi non metus tincidunt blandit. Praesent aliquam justo ipsum, at consectetur velit egestas sed. Ut elementum tortor sem, id hendrerit lectus finibus quis. Ut bibendum metus lectus. In bibendum libero et dictum rutrum. Quisque sed lobortis ligula, in consequat ex. Donec accumsan, felis ut pretium posuere, velit augue malesuada tellus, sed suscipit odio lectus at quam. Phasellus pharetra ornare tellus sed tempus. Mauris vel dapibus eros. ',
        ];

        for ($i = 0; $i < 20; $i++) {
            foreach ($events as $title => $description) {

                $startTime = new \DateTime();
                $startTime->add(\DateInterval::createFromDateString(rand(2, (200*24*60*60)) . ' seconds'));
                $startTime->setTime($startTime->format('H'), 0);
                $endTime = new \DateTime();
                $endTime->setTimestamp($startTime->getTimestamp());
                $endTime->add(\DateInterval::createFromDateString(rand(2, 24) . ' hours'));

                $event = new Event();
                $manager->persist($event
                    ->setTitle($title)
                    ->setDescription($description)
                    ->setActive(true)
                    ->setStartTime($startTime)
                    ->setEndTime($endTime)
                    ->setCity($cities[rand(0, (count($cities)-1))])
                    ->setCreatedBy($users[rand(0, (count($users)-1))])
                    ->setMaxParticipants((rand(1, 100)*10))
                );

                $manager->persist($slugger->sluggifyEntity($manager, $event, 'title'));
            }
        }

        $manager->flush();
    }
}
