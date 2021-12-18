
# Data

Data processing library which helps to read and write data to and from POPOs
in different formats.

## Usage

The following example showcases how you could read/write a complex model.

```php
// create processor
$processor = new Processor(Configuration::createDefault());

// example json data which we want to parse in our model
$in = <<<JSON
{
    "id": 1,
    "title": "Lorem ipsum",
    "author": {
        "id": 1,
        "name": "Foo",
        "email": "foo@bar.com",
    },
    "comments": [{
        "id": 1,
        "author": {
            "id": 1,
            "name": "Foo",
            "email": "foo@bar.com",
        },
        "text": "Lorem ipsum"
    },{
        "id": 2,
        "author": {
            "id": 1,
            "name": "Foo",
            "email": "foo@bar.com",
        },
        "text": "Lorem ipsum"
    }],
    "date": "2016-03-28T22:40:00Z"
}
JSON;

// reads the json data into a custom model class
$model = $processor->read(News::class, Payload::json($in));

// the model can be used to get or set data
$model->getAuthor()->getName();
$model->getComments()[0]->getText();

// writes the model back to json
$out = $processor->write(Payload::json($model));

// model classes
class News
{
    private ?int $id = null;
    private ?string $title = null;
    protected ?Author $author = null;
    /**
     * @var array<Comment>|null
     */
    private ?array $comments = null;
     #[Format('date-time')]
    private ?string $date;

    // getter/setter implementations removed for readability
}

class Author
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $email = null;

    // getter/setter implementations removed for readability
}

class Comment
{
    private ?int $id = null;
    private ?Author $author = null;
    private ?string $text = null;

    // getter/setter implementations removed for readability
}


```

## Formats

The library supports different reader and writer classes to produce different
data formats. If you want to read a specific format you can provide the content
type of the data. I.e. if you want read XML you could use the following
payload:

```php
$payload = Payload::create($in, 'application/xml');
```

The processor uses a reader factory to obtain the fitting reader for a specific
content type. In this case it would use the XML reader. The reader factory can
be easily extended with different reader classes to support other data formats.

```php
$configuration->getReaderFactory()->addReader(new Acme\Reader(), 32);
```

In order to produce a payload from an incoming HTTP request you simply have to
set the body as data and the content type from the header. How you access this
data depends on the HTTP interface. For an PSR-7 request you could use:

```php
$payload = Payload::create(
    (string) $request->getBody(),
    $request->getHeaderLine('Content-Type')
);
```

On the other hand if you want to write data as response you would use:

```php
$payload = Payload::create(
    $model,
    $request->getHeaderLine('Accept')
);
```

The writer factory can also be extended with custom writer implementations.

```php
$configuration->getWriterFactory()->addWriter(new Acme\Writer(), 64);
```

## Constraints

It is also possible to add specific constraints to your model class. In the following some examples:

```php
#[Required(['title'])]
class News
{
    #[Pattern('[A-z]')]
    private ?string $title = null;

     #[MinLength(3)]
     #[MaxLength(255)]
    private ?string $text = null;

    #[\PSX\Schema\Attribute\Enum(['active', 'deleted'])]
    private ?string $status = null;

    #[Minimum(0)]
    #[Maximum(5)]
    private ?int $rating = null;
}
```

All available attributes are located at the [psx/schema](https://github.com/apioo/psx-schema) project.

## Transformations

Each reader class returns the data in a form which can be easily processed. I.e.
the json reader returns a `stdClass` produced by `json_decode` and the xml
reader returns a `DOMDocument`. To unify the output we use transformation
classes which take the output of a reader and return a normalized format. I.e.
for xml content we apply by default the `XmlArray` transformer which transforms
the `DOMDocument`. So you can use a transformer if you directly want to work
with the output of the reader.

In case you want to validate incoming XML data after a XSD schema you could use
the `XmlValidator` transformer:

```php
$payload = Payload::xml($data);
$payload->setTransformer(new XmlValidator('path/to/schema.xsd'));

$model = $processor->read(News::class, $payload);

```

## Exporter

If you write data you can set as payload an arbitrary object. We use an exporter
class to return the actual data representation of that object. By default the
exporter reads also the psx/schema attributes so you can use the same model for
incoming and outgoing data. But it is also possible to use different classes.
I.e. you could create model classes using the psx/schema attributes only for
incoming data and for outgoing data you could use the JMS exporter in case you
have already objects which have these annotations.

If you have another way how to extract data of an object (i.e. a toArray
method which returns the available fields of the object) you can easily write
a custom exporter.
