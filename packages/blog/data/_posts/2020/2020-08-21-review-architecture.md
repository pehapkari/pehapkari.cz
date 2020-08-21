---
id: 87
title: "Review of the Advanced Web Application Architecture"
perex: |
    * This book will teach you how to structure applications that will last for years.
    * It is full of code examples that are easy to understand and follow.
    * I recommend this book, it is great.
author: 29
---


![Book](/assets/images/posts/2020/architecture-review/architecture_review.jpg)

## What is the Book About

Matthias shows how to focus on the business needs, on use cases and how to write it down into the code.
He calls this code "core code".
The core code is decoupled from databases, frameworks or libraries.
This is the general idea of the book - isolate the code containing business logic.

Part I explain why should we focus on separating core code from surrounding infrastructure code and step by step shows how to achieve that.
This separation leads to readable, maintainable, testable, extendable code that will survive at least years.
Part I is also fairly unit tested and explains how to write proper - fast and meaningful - unit tests.

Part II puts concepts from Part I into context, this is actually the architecture part.
Matthias explains here how to organize layers, what is hexagonal architecture and how to achieve it.
One whole chapter is dedicated for testing - what tests write for what layer.
Yes, there are plenty types of tests, not unit tests only.

## Writing Style & Code Examples

The book is using easy English and anyone who can read this review can read the book.
Matthias seamlessly leads reader from known but messy code to new well-structured approach.
Difficult topics like entities, repositories, ports, adapters, ... are explained so easily that most of the readers will understand them immediately.

This is a book for programmers, about half of the book are code examples.
Topics might be difficult to understand, but once we see the code in action, we immediately understand it.

Code examples speaks itself, the code is full of classes and interfaces with clear behavioral responsibility.
Definitely matured OOP worth following.

## Personal Impression

The power of suggested architecture is that it leads to software that is clear, testable and maintainable.
I just agree.

I surprisingly iterated over years to the same architecture.
So this book unfortunately confirms my architecture style and next time I'll face such challenge I'll be more resistant to different approach/architecture.

I recommend reading this book to everyone who
* develops (not only web) applications that have to survive at least couple of years
* develops applications in team
* wants to test code, but keep struggling
* wants to apply Domain-Driven Design

If you aren't interested in application architecture much,
but you somehow have a copy of the book in your hands than I strongly recommend reading chapters 9.1 and 9.3 first.
Really, just these chapters and then continue from 1.
I was missing a motivation, the **why** until these chapters, so don't worry and start with them.

Matthias doesn't use prefixes/suffixes in code examples like `*Interface`, and it makes me very satisfied.

The book has almost 400 pages, but I was able to read it fairy quickly due to easy english and enormous amount of code examples.

## Ideas that Made My Day

* It's ok to pass services to entities (chapter 3.1). Once we do it, we encapsulate the behavior into entity, where it belongs.
* Matthias nicely explains domain events (chapter 3.3) as a synchronization mechanism write -> read model.
Using domain events doesn't mean doing event sourcing.
* Matthias suggests taking a look on the use-case and imagine using CLI instead of web (chapter 4.1).
Helps to reach point of the book - infrastructure (technology) independent code.
* Architecture described in the book allows us postponing important decisions (chapter 4.5),
like choosing e-mail sending technology. This advantage should be highlighted more.
* Fast tests are important (chapter 5.7).
* Objects should talk to external systems via services (chapter 7.2). A simple rule that makes thinking about external world much easier.
* Eliminate code that forces us to jump from class to class, from layer to layer (chapter 7.5).
This "jumping" code is a sign of putting code in wrong layer.
* Entities are always valid (chapter 8.1).
* Validation means applying pure functions (chapter 8.5). Nice simplifying idea.
* Port = interface. Beautiful definition.
* Unit testing isn't about classes, it's about "testing behavior of object" (chapter 14).
If we need multiple classes for it, just use them.
* [`deptrac`](https://github.com/sensiolabs-de/deptrac) seems to be a tool that helps with the architecture.
Looking forward to using it in a real project.

## Chapters in Detail

I like most of the book, sometimes I disagree and sometimes I'm missing important information.
You can find here couple of my notes and confront them with your point of view.

### ✖ ORM Mappings in PHP Annotations (chapter 2.5.1)

Matthias suggests writing ORM mapping directly to PHP annotations

```php
/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
final class Order
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;
}
```

Including ORM mapping in the core code has disadvantages/problems
* Mixes two different languages - domain language and mapping language.
We read different languages for different reasons.
When we read the core code, we want to understand the logic.
When we read the mapping, we want to understand how it is persisted.
Once we mix these languages together, it is difficult to achieve the desired task because the code is obfuscated by a different language.
* Mapping in annotations makes PHP classes very long and steals readers focus.
* Matthias says that the mapping isn't infrastructure code because of rules he defined.
I disagree, it is infrastructure code because it is tied up to concrete technology.
* For me this approach is similar to mixing PHP and HTML code.

I strongly suggest writing ORM mapping in separate files, like XMLs.

### ✔ Don't Use Active Record (chapter 2.8)

Matthias discourages us from using active record design pattern.

This technique has several disadvantages, for me the most important are
* Makes unit testing almost impossible or very difficult
* Extending from base class means relation IS. So our object like Order suddenly IS an Entity.
Maybe sounds right at first look, but it means our Order IS and Entity of concrete library.
That makes the Order to be an infrastructure entity.

### ✔ Use Read Model (chapter 3)

Matthias introduces read models and view models without any questions, and I like it.
We need more view models and thinking that reading is very different from writing.

### ✔ Hide Low Level Details (chapter 3)

Matthias suggests to hide low level implementation like database queries behind abstractions. Sure, but why?

The reason for me is that high-level core code is easy to understand for new programmers (or for me two weeks later).
It's easier to maintain the logic if we are not overrun with implementation details.

### ❓ How to Solve Naming Collisions (chapter 3)

The book doesn't deal with possible naming collisions.

We have an `Order` as write model, and we may have `Order` in customer account, `Order` in API, `Order` send by email, ...
How to deal with multiple concepts that naturally have the same name?

I wouldn't recommend using invented names because than it would be difficult to understand the concept.
So I'd like to know what Matthias suggests.

I personally tried to distinguish these classes by namespaces, but result is messy.
Once I look for a class, I have several occurrences and have to think about namespaces.

A solution that works for me is to prefix read models by the purpose.
So I'd have `Order` - write entity, and read models - `CustomerAccountOrder`, `ApiOrder`, `EmailOrder`.
But is it good?

### ✖ Naming "Application Services" (chapter 4)

Application services are classes that encapsulate domain use-case, are isolated from infrastructure and belongs to core code.
One such service could be `OrderEbookService` with method `order()`. The concept makes sense.

I have a problem with the naming.
* Application service - the naming is too vague. There are so many things in IT called application service, so it can mean anything.
* `OrderEbookService` - I'm bothered by the suffix `Service`, it signs wrong naming for me, because programmers calls `*Service` anything they can't properly name.

Instead, I'd like to suggest naming that fits better and is even used later in the book - use-cases.
* Use Cases - layer of, well, use cases (that encapsulate domain use-case, are isolated ...)
* `OrderEbookUseCase` or `OrderEbook` with method `order()`

### ✖ Using Mocks in Tests (chapter 5.7)

Matthias suggests to use mocks in tests for external dependencies like `Translator`.

```php
interface Translator {
    public function trans(string $message, string $locale): string;
}

$translator = $this->createMock(Translator::class);
$translator->expects($this->once())
    ->method('trans')
    ->willReturn(
        function (string $message, string $locale): string {
            return $message . '(translated)';
        }
    );
```

In this situation when we have an interface, I'd rather use anonymous class (or implementation for tests).

```php
$translator = new class implements Translator {
    public function trans(string $message, string $locale): string {
        return $message . '(translated)';
    }
};
```

Benefits
* Shorter code
* Supports automatic refactoring in IDE
* Static analysis tools understands it and can detect problems

The anonymous implementation of course doesn't support checks like how many was called.
If we have such needs, we shall think more what are we testing and what do we expect the stub/mock does.
So there might be situations where mocks are more practical than anonymous implementation.

### ✖ Misinterpretation of Inversion of Control (chapter 5.9)

Matthias says that declaring required constructor arguments is called inversion of control, and we should never use service locator.

The statement is unfortunately wrong, inversion of control != constructor injection.

Inversion of control is a concept that says we shouldn't construct dependencies by ourself, and we should require them.
One implementation of inversion of control is a service locator and one implementation is a constructor dependency injection
(and there are a couple of more implementations).

I agree with the Matthias that dependency injection is better than service locator, but inversion of control isn't dependency injection.

### ✔ Behavior of Objects (chapter 7.4)

"Value objects should offer no behavior that hasn't been explicitly enabled and designed for your use case"

One of top highlights of the book.

I'd like to extend the statement to "any object".
Then the code is easy to understand because it does the job we need, no noise, no unused behavior.

This highlight reminds me frameworks/libraries that forces us to extend from base classes
while these base classes have tens of methods.
No, please, no.

### ✖ Validation (chapter 8)

This chapter starts well - value objects and entities are always valid.
Unfortunately the whole chapter feels unfinished, it is for me the weakest chapter in the book.

#### Multiple Validation Errors

Matthias suggests constructing value objects in validation layer to catch exceptions and perform validations,
and then construct them again in core code for business logic.
We can find this code listing 8.11 (chapter 8.3) and it isn't the best and even Matthias isn't satisfied with it.
We have to double check inputs - once in the validation layer and once in the core layer.
It is double effort for runtime, but also double effort for writing the code that may result in an inconsistency.

Then Matthias recommends that UI shouldn't allow us to make mistakes, and therefore we don't have to write such complicated code anymore.

Matthias conveniently forgets about API calls and following chapters don't provide viable solution.

#### Translatable Exceptions

Matthias suggests to use translatable strings (chapter 8.4) in domain exceptions and translate these string in UI layer to user readable error message.
Core code suddenly have an information that is useful only for the UI (not core layer).
So we mix layers, and that is a bad idea.

Possible solution is to name the exception class by the reason.
This is meaningful in core code - we'll need such information at least in tests, and exception name can be still translated in UI layer.

#### Different Exceptions

The book unfortunately doesn't distinguish runtime (catchable) and logic exceptions (chapter 8.4).

Logic exception is an error caused by a programmer, like `InvalidArgumentException` or `TypeError`.
These exceptions should never be caught and translated to user errors because user can't do anything about it.

Runtime exceptions is something that can occur only in runtime, like `ValidatorException`, `UnexpectedValueException` or `IOException`.
Users can usually fix problems by themselves so such exceptions should be translated to user readable error message.

#### Double Effort Solution

We touched this topic in [Multiple Validation Errors](#multiple-validation-errors) and we'll combine it with commands.

Matthias suggests to
* Validate data in UI using value objects
* Pass only raw data to commands
* Create value objects in application services

I have the problem with double creation of value objects.

To solve this problem, I suggest passing value objects to commands.
It solves all problems because we create value object only once.
Therefore, there is no runtime overhead, and can't lead to inconsistency.

### ✔ Structure of Application

Matthias suggests following structure of application
* Domain
* Application
* Infrastructure
  * Symfony
  * Doctrine
  * Technology X

Yes, this is the way to structure application that isn't framework-centric, but core code centric.
An application that should survive surrounding technologies.

MVC is really not enough for a domain focused application that should last years.

### ✖ Connecting UI and Application Service Layers (chapter 13)

The whole chapter focuses on connecting layers.
The only thing that bothers me is connection between UI and Application Service layers.
Matthias suggests two solution for this topic.

Create an interface to the application layer with all necessary methods. Eg.:
```php
interface ApplicationIterface {
    public function order(/* parameters */): OrderId;
    public function listProducts(): array;
    public function authenticateUser(/* parameters */): UserId;
    // ...
}
```
This solution horrifies me when I imagine how many methods will be there.
Not only the amount of methods is problem, also every controller using this interface will use just one (or few) methods,
but will always receive all of them.

Another solution uses commands and command handler:
```php
class CreateOrderCommand {
    private array $products;
    // parameters, constructor, getters
}

class OrderService {
    public function handle(CreateOrderCommand $command): OrderId {
        // behavior
    }
}

interface CommandBus {
    /**
     * @param mixed $command
     * @return mixed
     */
    public function handle($command);
}

class OrderController {
    private CommandBus $commandBus;
    public function createOrder(Request $request): Resposne {
        $command = new CreateOrderCommand(/* ... */);
        $result = $this->commandBus->handle($command);
        // $result is mixed ?!
    }
}
```

This solution is bit different.
Clean in separation, nice for tests, but we have no idea what the command bus returns or throws.
We can do some tricks like `assert()` but the code doesn't provide information to the reader what is really returns.

Return value could be solved by command handlers that don't return anything.
```php
class OrderService {
    public function handle(CreateOrderCommand $command): void {
        // behavior
    }
}
```

This works.
One problem here could be dealing with generated ID, but this has a solution as well.
```php
class OrderController {
    private CommandBus $commandBus;
    private OrderIdGenerator $orderIdGenerator;
    public function createOrder(Request $request): Resposne {
        $orderId = $this->orderIdGenerator->generate();

        $command = new CreateOrderCommand($orderId, /* ... */);
        $this->commandBus->handle($command);
    }
}
```

The problem with thrown exceptions is still here.
We have no idea what exception the handler may throw.
I don't have a solution for that.

### ✖ Contract Testing (chapter 14.3)

Testing adapters (eg. repositories) should be done by testing heir public methods only.
This idea is beautiful, but may not test enough.

```php
interface Repository {
    public function get(OrderId $orderId): Order;
    public function save(Order $order);
}
```

If we test a `DoctrineRepository` that implements this interface just by the contract, we may end up with the same instance that we've stored.
```php
$orderId = new OrderId();
$order = new Order($orderId, /* parameters */);
$this->repository->save($order);

$foundOrder = $this->repository->get($orderId);
assert($foundOrder === $order); //true
```

This is because Doctrine holds entity map, and when we request an object that is already in memory, Doctrine returns it.

This means the test didn't test entities loading at all.
So if we forget to map a new property into a database, this test may not discover it.

A test ensuring loading object have to clean the EntityManager.
```php
$orderId = new OrderId();
$order = new Order($orderId, /* parameters */);
$this->repository->save($order);

$entityManager->clear();

$foundOrder = $this->repository->get($orderId);
assert($foundOrder === $order); //false! Not the same instance
```

Such a test isn't contract test anymore.

I solved this issue by [contract test](https://github.com/simara-svatopluk/cart/blob/master/tests/Infrastructure/CartRepositoryTest.php)
that defines abstract method for flushing, and such flushing is defined in a [concrete technology test](https://github.com/simara-svatopluk/cart/blob/master/tests/Infrastructure/DoctrineCartRepositoryTest.php#L24).

### ✔ Gherkin Scenario Based Testing (chapter 14.5)

Matthias provides how-to tutorial for Gherkin scenario based tests.

Such test looks like
```gherkin
Given the user has not ordered yet
When the user adds a book with price 100€ into the cart
Then the shopping cart total is 90€
```

This is a new approach for me, and I'm looking to use in next project. Because it has several benefits.
* Scenarios written in human language
* Instant documentation
* Forces us to focus on behavior, not data

By the way, scenarios tests the core code only, so tests are still fast.

### ✔ Development Workflow (chapter 14.7)

Matthias focuses on communication between programmers and other company members.
That's in my opinion the most important aspect to deliver software that has value and lasts.

This is one of the best chapters in the book which also describes where to start with development and how to proceed.

### ➕ Topics I Miss in the Book
* Matthias often mentions that proposed architecture helps with "framework migration".
That is quite rare case, similar to migration to a different database.
What is often case is "framework upgrade". The proposed architecture makes upgrading frameworks and libraries much easier.
* I miss an important discussion when we start using domain events for delegating job (chapter 4.5).
Is sending e-mail part of the order use-case and when it fails, ordering should fail?
If no, using domain events and subscribers is fine.
But if yes, we should keep sending e-mails in the application service.
* Application service can accept not only primitive types but also value objects.
I find sending value objects to application services pretty useful because then the use-case has fewer reasons to fail.

## Thank You

Matthias, thank you for the book, it is great.

The detail review may look like I disagree with lots of things, but it's not true.
I wanted to explain why do I disagree and suggest an alternative.
I'm open for discussion!

I in fact agree with most of the book, and when I skipped a chapter in the review it means I have nothing to add, I just agree.


## Contact

Do You want to improve your architecture?. Hire me, I'll help You [svatasimara.cz](http://svatasimara.cz/)
