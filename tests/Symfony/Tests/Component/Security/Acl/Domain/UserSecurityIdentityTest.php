<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\Security\Acl\Domain;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

class UserSecurityIdentityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $id = new UserSecurityIdentity('foo', 'Foo');

        $this->assertEquals('foo', $id->getUsername());
        $this->assertEquals('Foo', $id->getClass());
    }

    /**
     * @dataProvider getCompareData
     */
    public function testEquals($id1, $id2, $equal)
    {
        $this->assertSame($equal, $id1->equals($id2));
    }

    public function getCompareData()
    {
        $account = $this->getMockBuilder('Symfony\Component\Security\Core\User\AccountInterface')
                            ->setMockClassName('USI_AccountImpl')
                            ->getMock();
        $account
            ->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('foo'))
        ;

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token
            ->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($account))
        ;

        return array(
            array(new UserSecurityIdentity('foo', 'Foo'), new UserSecurityIdentity('foo', 'Foo'), true),
            array(new UserSecurityIdentity('foo', 'Bar'), new UserSecurityIdentity('foo', 'Foo'), false),
            array(new UserSecurityIdentity('foo', 'Foo'), new UserSecurityIdentity('bar', 'Foo'), false),
            array(new UserSecurityIdentity('foo', 'Foo'), UserSecurityIdentity::fromAccount($account), false),
            array(new UserSecurityIdentity('bla', 'Foo'), new UserSecurityIdentity('blub', 'Foo'), false),
            array(new UserSecurityIdentity('foo', 'Foo'), new RoleSecurityIdentity('foo'), false),
            array(new UserSecurityIdentity('foo', 'Foo'), UserSecurityIdentity::fromToken($token), false),
            array(new UserSecurityIdentity('foo', 'USI_AccountImpl'), UserSecurityIdentity::fromToken($token), true),
        );
    }
}
