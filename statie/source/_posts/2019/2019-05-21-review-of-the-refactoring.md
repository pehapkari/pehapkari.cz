---
id: 82
title: "Book Review: Refactoring"
perex: |
    Review of the Refactoring: Improving the Design of Existing Code by [Martin Fowler](https://martinfowler.com/), 2nd edition (2018).
author: 29
tweet: "New Post on #pehapkari Blog: Review of the Refactoring book #refactoring #martinfowler"
---

![Refactoring book](https://martinfowler.com/books/refact2.jpg)

The book starts right away with an example of low-quality code and our goal is to add a feature.
Fowler attracts our attention as we immediately realize the need for refactoring.
Then we apply one refactoring after another, improve the code structure and after a couple of pages, we are able to add the feature without any additional effort.
The code is simply prepared for the feature to be added.

The author explains the importance of refactoring in a natural way - we realize we need to refactor, there is no other option.
Also often emphasizes the importance of automated tests because we cannot refactor without a proper test suite.
The first step of refactoring is to create tests and the book explains how to start with tests (but there are better sources for starting with tests like [TDD by Kent Beck](https://www.amazon.com/Test-Driven-Development-Kent-Beck/dp/0321146530?tag=uuid10-20)).

Honestly, the important part of the book is the first 100 pages (of 400) and I recommend for every developer to read the first part of the book, the remaining part of the book is optional.

## Catalogue of Refactorings

The remaining 300 pages is a catalog of refactoring and you can find it [online](https://refactoring.com/catalog/).
Each refactoring includes motivation - when, mechanics - how - in step-by-step form, and examples.
I like the step-by-step instructions when tests pass after each instruction.
It is awkward to apply these steps for simple refactorings, and powerful to apply for complicated refactorings or refactorings that involves a lot of occurrences.

All refactorings are explained really simply, even the difficult parts.
That's not because oversimplification, but it's because the author is a professional teacher, the explanation is just great.
Most of the refactorings are simple (sometimes too much like [Remove Dead Code](https://refactoring.com/catalog/removeDeadCode.html)) but still practical.

## Personal Impression

Almost all refactorings include inverse refactoring.
When there is [Extract Variable](https://refactoring.com/catalog/extractVariable.html), you can find also [Inline Variable](https://refactoring.com/catalog/inlineVariable.html).
That was a surprise for me as I thought the first option is always better (and I learned that's not true).

Extracting variable gives a name to the statement
```php
return preg_replace('/(?:[,.]0+|([,.]\d*?)0+)$/', '$1', $value);

// refactored to
$croppingZerosPattern = '/(?:[,.]0+|([,.]\d*?)0+)$/';
return preg_replace($croppingZerosPattern, '$1', $value);
```

On the other hand when the statement is self-explanatory, we can inline the variable
```php
$itemsPrice = $this->getItemsPrice();
if ($itemsPrice > $this->discountTreshold) {

// refactored to
if ($this->getItemsPrice() > $this->discountTreshold) {
```

I want to point out [Replace Type Code with Subclasses](https://refactoring.com/catalog/replaceTypeCodeWithSubclasses.html) and [Introduce Special Case](https://refactoring.com/catalog/introduceSpecialCase.html).
Both introduce reasonable inheritance.
Reasonable inheritance is the most difficult part of OOP for me and these refactorings taught me something new.

Calculating coupon discount refactored from anemic model to objects using inheritance
```php
if ($coupon->getType() === Coupon::PERCENTAGE) {
    $cart = $coupon->applyPercentaceDiscount($cart);
} elseif ($coupon->getType() === Coupon::ABSOLUTE) {
    $cart = $coupon->applyAbsoluteDiscount($cart);
}

// refactored to
abstract class Coupon {
    abstract public function applyDiscount(Cart $cart): Cart;
}
final class PercentageCoupon extends Coupon {
    public function applyDiscount(Cart $cart): Cart {
        // logic formerly in applyPercentaceDiscount
    }
}
final class AbsoluteCoupon extends Coupon {
    public function applyDiscount(Cart $cart): Cart {
        // logic formerly in applyAbsoluteDiscount
    }
}

$cart = $coupon->applyDiscount($cart);
```

When there is no coupon filled, the special case handles it elegantly
```php
if ($coupon !== null) {
    $cart = $cart->applyDiscount($cart);
}

// refactored to
class NoCoupon extends Coupon {
    public function applyDiscount(Cart $cart): Cart {
        return $cart;
    }
}

$cart = $cart->applyDiscount($cart);
```

On the other hand, most of the refactorings were pretty simple or already known to me and I'd like to see more advanced refactorings, that's what I'm missing in the book.

## Language & Code

The book is easy to read, the author is using easy English and anyone who can read this review can read the book.

Code snippets are written in JavaScript, are simple and usually pretty short.
I saw a couple of pages of the 1st edition of the book and I can only congratulate Fowler for choosing the JavaScript because original Java code is really verbose.
Just compare

#### 2nd edition in JavaScript

```js
movie = {'name': "Pulp fiction"};
```

#### 1st edition in Java

```java
class Movie {
    private String name;
    public Movie(String name) {
        this.name = name;
    }
    public String getName() {
        return name;
    }
}

Movie movie = new Movie("Pulp Fiction");
```

this is not a Java criticism, JavaScript is just better for educative purposes in this case.

## Contact

Do you need help with refactoring your legacy code? Hire me, I can help you - [svatasimara.cz](http://svatasimara.cz/)
