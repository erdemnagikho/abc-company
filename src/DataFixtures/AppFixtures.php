<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUserTypes($manager);
        $this->loadUsers($manager);

        $this->loadCategories($manager);
        $this->loadProducts($manager);
    }

    public function loadUserTypes(ObjectManager $manager)
    {
        $userType = new UserType();
        $userType->setName("Admin");
        $this->addReference('user_admin', $userType);
        $manager->persist($userType);

        $userType = new UserType();
        $userType->setName("Customer");
        $this->addReference('user_customer', $userType);
        $manager->persist($userType);

        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $type_admin = $this->getReference('user_admin');
        $type_customer = $this->getReference('user_customer');

        $user = new User();
        $user->setUsername("admin");
        $user->setName("Admin");
        $user->setEmail("admin@abc-company.com");
        $user->setType($type_admin);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret123Admin'
        ));
        $manager->persist($user);

        $user = new User();
        $user->setUsername("kirmizi_ticaret");
        $user->setName("Kırmızı Ticaret");
        $user->setEmail("kirimizi_ticaret@abc-company.com");
        $user->setType($type_customer);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret123Kirmizi'
        ));
        $manager->persist($user);

        $user = new User();
        $user->setUsername("beyaz_ticaret");
        $user->setName("Beyaz Ticaret");
        $user->setEmail("beyaz_ticaret@abc-company.com");
        $user->setType($type_customer);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret123Beyaz'
        ));
        $manager->persist($user);

        $user = new User();
        $user->setUsername("siyah_ticaret");
        $user->setName("Siyah Ticaret");
        $user->setEmail("siyah_ticaret@abc-company.com");
        $user->setType($type_customer);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret123Siyah'
        ));
        $manager->persist($user);

        $manager->flush();
    }

    public function loadCategories(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName("Electronic");
        $category->setSlug("electronic");
        $this->addReference('electronic_category', $category);
        $manager->persist($category);

        $category = new Category();
        $category->setName("Computer");
        $category->setSlug("computer");
        $this->addReference('computer_category', $category);
        $manager->persist($category);

        $manager->flush();
    }

    public function loadProducts(ObjectManager $manager)
    {
        $category_electronic = $this->getReference('electronic_category');
        $category_computer = $this->getReference('computer_category');

        $product = new Product();
        $product->setName("Nintendo Switch");
        $product->setSlug("nintendo_switch");
        $product->setStock(100);
        $product->setCategory($category_electronic);
        $manager->persist($product);

        $product = new Product();
        $product->setName("Fujitsu ScanSnap");
        $product->setSlug("fujitsu_scansnap");
        $product->setStock(150);
        $product->setCategory($category_electronic);
        $manager->persist($product);

        $product = new Product();
        $product->setName("ASUS Chromebook Flip");
        $product->setSlug("asus_chromebook_flip");
        $product->setStock(150);
        $product->setCategory($category_computer);
        $manager->persist($product);

        $product = new Product();
        $product->setName("Acer Aspire");
        $product->setSlug("acer_aspire");
        $product->setStock(150);
        $product->setCategory($category_computer);
        $manager->persist($product);

        $manager->flush();
    }
}
