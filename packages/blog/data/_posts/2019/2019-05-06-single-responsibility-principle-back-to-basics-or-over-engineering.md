---
id: 80
title: "Single Responsibility Principle: Back to basics or over-engineering?"
perex: |
    We'll focus on responsibility by answering the question *What is it responsible for?* or *What should it do?*
    I find this question one of the most important for software development as it makes easier to decide where should a part of software be.
    We'll cover methods, classes and even modules by the single responsibility principle.
author: 29
tweet: "New Post on #pehapkari Blog: Single Responsibility Principle - back to basics or over-engineering? #singleresponsibility #solid"
---
To be clear we'll not focus on responsibility from the SOLID point of view as *reason for change*.

## Money class
I worked on a shop system that has to deal also with money.
We had troubles with money representation (sometimes floats, integers or strings) so we refactored the money concept into a value object to make the concept implicit.
We represented money as a decimal number, eg. `500.50` (without currency as it was impossible to refactor also the currency concept in the same time).
We tested a couple of libraries that deal with money but none of them fitted our use-cases perfectly. So we created a custom value object that is a proxy to a library.

The Money value object is responsible for dealing with money precisely in the shop.
It represents money such as `500.50` and is also responsible for calculations like adding, subtracting, precisely dividing and so on.
The interface looks like
```php
class Money {
    public function add(Money $money): Money;
    public function subtract(Money $scale): Money;
    public function equals(Money $money): bool;
    public function getAmount(): string; //eg. "500.50"
    // ...
}
```
Great so far, a clear single responsibility.

## Creating money

There are two ways to create the money object - from an integer `500` or from a string `"500.50"`.
In the following example, we'll use static constructors.
Let's discuss two ways of the implementation.

### One creating method

PHP is a dynamic language so there can be only one method that is used for creating the money object

```php
class Money {
    /**
     * @param int|string $value
     */
    public static function create($value): self {
        // ...
    }
}
```

Usage is pretty straightforward
```php
$integerMoney = Money::create(100);
$stringMoney = Money::create("500.50");

$inputMoney = Money::create($userInput);
```

Let's implement the method completely

```php
class Money {
    // ...

    /**
     * @param int|string $value
     * @throws UnsupportedTypeException
     * @throws MalformedMoneyStringException
     */
    public static function create($value): self {
        if (is_int($value)) {
            return new self((string) $value);
        }

        if (is_string($value)) {
            $this->validateString($value);
            return new self($string);
        }
        throw new UnsupportedTypeException($value, ['string', 'int']);
    }

    /**
     * @throws MalformedMoneyStringException
     */
    private function validateString(string $value) {...}
}
```

The `create` method is responsible for
* creating the money object
* checking the argument type
* throwing an exception when the argument is not supported

This is quite a lot of responsibilities.
The method must also throw exceptions and the user must deal with them.

#### Creating from integer
```php
try {
    $integerMoney = Money::create(100);
} catch (UnsupportedTypeException | MalformedMoneyStringException $e) {
    // do nothing this cannot happen
}
```

We know that we are creating money from integer, and in this case, nothing wrong can happen.
But due to the method interface, we have to catch exceptions that possibly can be thrown (but never are).
Really awkward is catching the `MalformedMoneyStringException` as we are not passing a string.

#### Creating from string

```php
try {
    $integerMoney = Money::create("500.50");
} catch (UnsupportedTypeException $e) {
    // do nothing this cannot happen
} catch (MalformedMoneyStringException $e) {
    // ... deal with the problem
}
```

Creating from the string is a bit more reasonable.
The input string can be malformed and we have to deal with such a situation.
But again, the `UnsupportedTypeException` can't occur, but because of the interface, we have to deal with the exception anyway.

These problems are caused by the fact `create` method has multiple responsibilities.
Let's try to break the responsibility into two methods.

### Two creating methods

We'll have two methods for creating the money object `createFromInteger(int)` and `createFromString(string)`.

Usage will be
```php
$integerMoney = Money::createFromInteger(100);
```

```php
try {
    $stringMoney = Money::createFromString("500.50");
} catch (MalformedMoneyStringException $e) {
     // ... deal with the problem
}
```

Both methods have a clear responsibility.
Create money from an integer.
Create money from a string.

There is no responsibility for checking the argument type, selecting behavior and throwing an exception when the argument is wrong.

The implementation
```php
class Money {
    // ...

    public static function createFromInteger(int $value): self {
        return new self((string) $value);
    }

    /**
     * @throws MalformedMoneyStringException
     */
    public static function create(string $value): self {
        $this->validateString($value);
        return new self($string);
    }

    /**
     * @throws MalformedMoneyStringException
     */
    private function validateString(string $value);
}
```

Such code has a couple of benefits
* simpler
* easy to understand
* fewer exception states
* safer - uses types

We moved the decision responsibility to the caller, so the caller must know what type is passing.
But it is natural - the caller knows the argument type anyway, so there is no added effort.

## JSON Serialization
We have a need - to serialize the Money value object to JSON for JavaScript

```json
{ "amount":  "500.50"}
```

Let's discuss two implementations and again from the responsibility point of view.

### Internal serialization
```php
class Money implements JsonSerializable {
    // ...
    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->getAmount(),
        ];
    }
}
```

This is a quite common approach in PHP, now the caller can use simple
```php
$stringJson = json_encode($money);
```

Let's stop for a while. **What does the money class do? What is it responsible for?**

Right now, the class is responsible for dealing with money AND for JSON serialization.
Once we use AND during explaining the responsibility, we should be alert, maybe we are doing something wrong.

The danger of mixed responsibility is the class is difficult to understand.
All methods deal somehow with the money problem and suddenly there is one method that deals with JSON serialization.
The second responsibility breaks the mental model we build in the head as we are trying to understand the class.

Mixed responsibility also makes usage more difficult.
If we want to use JSON as the class provides, it is usable.
But once we need a different JSON format, we have to change the serialization method first.
And the class becomes more and more complex.

To be honest there may be use-cases for the internal JSON serialization.
If an object is serialized to JSON heavily in the system, then the internal serialization could make sense as it is a common use-case.
But it is not the money case, at least not in this system.

### External serialization
```php
class JsonMoneySerializer {
    public function serialize(Money $money): string {
        return [
            'amount' => $money->getAmount(),
        ];
        return json_encode($array);
    }
}
```
Usage
```php
$stringJson = $this->jsonMoneySerializer->serialize($money);
```
Serializer has a clear responsibility - serializing money to JSON.
Money class is not changed and is not messed by the serialization code.

When we'll need a different JSON structure, we'll create a new serializer.
We won't have to touch the current code, so we're less likely to break it.
We end up with short, even dumb, code that is easy to understand and combine.

## Price module

We have a module that is responsible for the price.
It answers questions like
- *What is the price of the product?*
- *What is the price of the shipping?*
- *What is the overall price of the order?*

Now we have a new use case *Sort products by price*.
Should this behavior be a part of the pricing module?
Well maybe yes and maybe no.

A good practice I found is to put this new responsibility into a separate module that uses the price module.
As we understand more, we may find that the separation was a good idea because the new module is at the end responsible for preparing data for the web,
caching, and that is a clear separated responsibility.

If we add this new responsibility into an existing module, it will start growing.
*Where should I put price caching?* Into the price module, it deals with price.
*Where should I put preparing price data for web?* Into the price module, it still fits there.
And we end up with one huge module that has multiple responsibilities, is difficult to understand and nearly impossible to maintain as all pieces depend on each other.

### When splitting doesn't make sense

On the other hand, we may later find that this new module was never used for anything else than sorting and that it would be more meaningful
to have the functionality within the price module.
In such a case, it is a good idea to merge these two modules into one with extended responsibility.

The trick is that merging two modules into one is often much easier than splitting one module into two.
So generally I suggest starting with a separated module for a responsibility that seems to not fit into an existing module
while keeping an option to merge them later.

## Single responsibility of a variable

Let's read following code

```php
$object = new Category($categoryId, $categoryName);
$this->persist($object);

$object = new Article($articleId, $text);
$this->persist($object);
```

What is the responsibility of the `$object` variable?
Or what does the `$object` variable contain?
It contains category or article depending on what time are we asking for.

This is a common violation of single responsibility caused by reusing variables.
The solution is simple and obvious

```php
$category = new Category($categoryId, $categoryName);
$this->persist($category);

$article = new Article($articleId, $text);
$this->persist($article);
```

This small refactoring caused completely clear variable responsibility.
The code is so easy to understand that we can call it even boring.

## Over-engineering

If we take a single responsibility too far, it is easy to over-engineer the code.

*Should be adding money responsibility of the Money class?*
Once we answer no, crazy things can happen.
We can come up with `MoneyAdder`, `MoneySubtractor`, `MoneyMultiplier`, `MoneyComparer`, `IntegerMoneyFactory` and so on and so on.
And that is usually a sign of over-engineering.

## Conclusion

Code that follows the single responsibility principle is easy to read, understand, use and change.
We have to think about responsibilities in all scales - variables, methods, classes, and even modules.
We should keep asking *What should it do?* *What does it do?* *What is it responsible for?*
Once we use AND during answering, we know there is something fishy.

So what do you think?
Back to basics or over-engineering?

## Contact

Do you struggle with difficult code and like the Single Responsibility approach? Hire me, I can help you - [svatasimara.cz](http://svatasimara.cz/)
