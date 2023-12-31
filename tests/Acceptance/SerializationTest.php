<?php

namespace Dontdrinkandroot\ActivityPubCoreBundle\Tests\Acceptance;

use Dontdrinkandroot\ActivityPubCoreBundle\Model\Type\Core\CoreObject;
use Dontdrinkandroot\ActivityPubCoreBundle\Model\Type\CoreType;
use Dontdrinkandroot\ActivityPubCoreBundle\Model\Type\Extended\Actor\Person;
use Dontdrinkandroot\ActivityPubCoreBundle\Model\Type\Property\Uri;
use Dontdrinkandroot\ActivityPubCoreBundle\Serializer\ActivityStreamEncoder;
use Dontdrinkandroot\ActivityPubCoreBundle\Tests\WebTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class SerializationTest extends WebTestCase
{
    use SerializationTestTrait;

    public function testContextSingleValueSerialization(): void
    {
        $serializer = self::getService(SerializerInterface::class);
        $json = <<<JSON
{
    "@context": "https://www.w3.org/ns/activitystreams",
    "id": "https://example.com/abc",
    "type": "Note",
    "content": "This is a note"
}
JSON;
        $expectedJson = <<<JSON
{
    "@context": "https://www.w3.org/ns/activitystreams",
    "type": "Note",
    "id": "https://example.com/abc",
    "content": "This is a note"
}
JSON;

        $object = $serializer->deserialize($json, CoreType::class, ActivityStreamEncoder::FORMAT);
        $restoredJson = $serializer->serialize($object, ActivityStreamEncoder::FORMAT);
        self::assertEquals($expectedJson, $restoredJson);
    }

    public function testContextArraySerialization(): void
    {
        $serializer = self::getService(SerializerInterface::class);
        $json = <<<JSON
{
    "@context": ["https://www.w3.org/ns/activitystreams",{"@language": "en"}],
    "id": "https://example.com/abc",
    "type": "Note",
    "content": "This is a note"
}
JSON;
        $expectedJson = <<<JSON
{
    "@context": [
        "https://www.w3.org/ns/activitystreams",
        {
            "@language": "en"
        }
    ],
    "type": "Note",
    "id": "https://example.com/abc",
    "content": "This is a note"
}
JSON;

        $object = $serializer->deserialize($json, CoreType::class, ActivityStreamEncoder::FORMAT);
        $restoredJson = $serializer->serialize($object, ActivityStreamEncoder::FORMAT);
        self::assertEquals($expectedJson, $restoredJson);
    }

    public function testAdditionalPropertiesSerialization(): void
    {
        $serializer = self::getService(SerializerInterface::class);
        $json = <<<JSON
{
    "@context": "https://www.w3.org/ns/activitystreams",
    "id": "https://example.com/abc",
    "type": "Note",
    "content": "This is a note",
    "additionalScalar": "property",
    "additionalObject": {
        "foo": "bar"
    },
    "additionalArray": [
        "foo",
        "bar"
    ]
}
JSON;
        $expectedJson = <<<JSON
{
    "@context": "https://www.w3.org/ns/activitystreams",
    "type": "Note",
    "id": "https://example.com/abc",
    "content": "This is a note",
    "additionalScalar": "property",
    "additionalObject": {
        "foo": "bar"
    },
    "additionalArray": [
        "foo",
        "bar"
    ]
}
JSON;

        $object = $serializer->deserialize($json, CoreType::class, ActivityStreamEncoder::FORMAT);
        $restoredJson = $serializer->serialize($object, ActivityStreamEncoder::FORMAT);
        self::assertEquals($expectedJson, $restoredJson);
    }

    public function testSerializationMissingContext(): void
    {
        $serializer = self::getService(SerializerInterface::class);
        $coreObject = new CoreObject();
        $coreObject->id = Uri::fromString('https://example.com/abc');
        $coreObject->summary = 'Mary had a little lamb';
        $json = $serializer->serialize($coreObject, ActivityStreamEncoder::FORMAT);
        $expectedJson = <<<JSON
{
    "@context": "https://www.w3.org/ns/activitystreams",
    "type": "Object",
    "id": "https://example.com/abc",
    "summary": "Mary had a little lamb"
}
JSON;
        self::assertEquals($expectedJson, $json);
    }

    public function testActorWithSharedInbox(): void
    {
        $serializer = self::getService(SerializerInterface::class);
        $json = <<<JSON
{
    "@context": "https://www.w3.org/ns/activitystreams",
    "id": "https://example.com/abc",
    "type": "Person",
    "inbox": "https://example.com/abc/inbox",
    "endpoints": {
        "sharedInbox": "https://example.com/inbox"
    }
}
JSON;

        $person = $serializer->deserialize($json, CoreType::class, ActivityStreamEncoder::FORMAT);
        self::assertInstanceOf(Person::class, $person);
        self::assertEquals(Uri::fromString('https://example.com/abc/inbox'), $person->inbox);
        self::assertEquals(Uri::fromString('https://example.com/inbox'), $person->endpoints['sharedInbox'] ?? null);

        $restoredJsonArray = json_decode(
            $serializer->serialize($person, ActivityStreamEncoder::FORMAT),
            true,
            JSON_THROW_ON_ERROR
        );
        self::assertEquals([
            '@context' => 'https://www.w3.org/ns/activitystreams',
            'type' => 'Person',
            'id' => 'https://example.com/abc',
            'inbox' => 'https://example.com/abc/inbox',
            'endpoints' => [
                'sharedInbox' => 'https://example.com/inbox'
            ]
        ], $restoredJsonArray);
    }
}
