---
id: 83
title: "Logic Belongs to Entities"
perex: |
    The business logic belongs to entities.
    This sentence is being said over and over again in books and during conferences.
    But the sad truth is that a lot of large application has an anemic model and it seems to be impossible to refactor the difficult logic to entities.
    But is it really impossible?
author: 29
tweet: "New Post on #pehapkari Blog: Logic belongs to Entities... and how to do it #OOP"
---

Encapsulating the business logic into entities is especially difficult when the logic involves something more.
_Something more_ could be a different module, external system, database, ...

## Example

We have a Cart in the online store system, the Cart is responsible for handling items (add, remove) but not prices.
Once we want to show the total price of the Cart, we have to use fresh prices from the database because product prices are changed often.

```php
class Money {
    // properties

    // static constructor
    public static function fromString(string $input): Money;

    public function multiply(float $multiplier): Money;
    public function toString(): string; // 1500 €

    /**
     * @param $arrayOfMoney Money[]
     */
    public static function sum(array $arrayOfMoney): Money;
}

class Item {
    // properties, constructor

    public function getAmount(): float; // 4.0
    public function getProductId(): int // 123
}
```

## Logic in Service

Let's start with a service implementation that can be usually found in large applications (usually and sadly).
The benefit of this implementation is that it works

```php
class Cart {
    /**
     * @return Item[]
     */
    public function getItems(): array;

    // constructor
}

class CartTotalPriceService {
    /**
     * @var PDO
     */
    private $pdo;

    // constructor

    public function calculate(Cart $cart): Money {
        $prices = [];
        foreach ($cart->getItems() as $item) {
            $price = $this->refreshPrice($item->getProductId());
            $prices[] = $price->multiply($item->getAmount());
        }

        return Money::sum($prices);
    }

    private function refreshPrice(int $productId): Money {
        $statement = $this->pdo->query("SELECT price FROM product WHERE id = :id");
        $statement->bindParam(':id', $productId);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $number = (string) $result['price'];
        return Money::fromString($number);
    }
}

// usage
$totalPrice = $this->cartTotalPriceService->calculate($cart);

```

We can use Doctrine, our favorite ORM or a different database layer instead of PDO, but the code will be similar.

As we can see, we collect data from Cart entity and then do the business logic.
This approach is difficult to test, breaks encapsulation, leads to an anemic model with a logic outside of entities.
Not so good outcome.

## Think About Use-Case Only

The theme of this article is logic in entities, but let's think about the use-case **Calculate Cart Total Price** without programming first.
To be able to finish the use-case we need
* a Price of each Product
* Amount of each of Products

That's all we need.
It doesn't matter if the use-case is implemented in service or entity, we need all this information in both implementation.

## Moving Logic to Cart Entity

Our goal is to implement calculating the total price in the Cart entity itself.

We already know the amount of each of the products in the Cart, so there is no problem.

But we also need to know the price of each Product in the Cart.
We don't need to know prices in the whole Cart, we need to know prices only during the calculation of the total price.
So if we pass **Product Prices** just to the calculating method, we solve the problem.

And we can achieve **Product Prices** by an interface

```php
interface ProductPrices {
    public function refreshPrice(int $productId): Money;
}
```

Now we can pass this interface into the calculation method that sits in the Cart

```php
class Cart {
    /**
     * var Item[]
     */
    private $items;

    // constructor

    public function calculateTotalPrice(ProductPrices $productPrices): Money {
        $prices = [];
        foreach ($this->items as $item) {
            $price = $productPrices->refreshPrice($item->getProductId());
            $prices[] = $price->multiply($item->getAmount());
        }

        return Money::sum($prices);
    }
}

// usage
$totalPrice = $cart->calculateTotalPrice($this->productPrices);
```

As we can see, the method is logically as same as it was in `CartTotalPriceService`.
But there are a couple of important differences
* The logic sits together with data in the Cart entity, this leads to a rich model
* The Cart doesn't expose data (Items), Items are therefore properly encapsulated
* This code is testable as we can mock the interface in tests to produce predictable prices
* By the way, this was our goal - to have the logic in the entity

### Interface Implementation

The interface has to be implemented anyway, but that's not a problem at all
```php
class PDOProductPrices implements ProductPrices {
    /**
     * @var PDO
     */
    private $pdo;

    // constructor

    public function refreshPrice(int $productId): Money {
        $statement = $this->pdo->query("SELECT price FROM product WHERE id = :id");
        $statement->bindParam(':id', $productId);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $number = (string) $result['price'];
        return Money::fromString($number);
    }
}
```

Do you remember `CartTotalPriceService`? The code is the same.

And again we can use our favorite ORM, or database layer to implement the interface.
We can do even more, the implementation can use different system module or even API to a different system.
We are not limited by technology, all thanks to the interface.

#### Only One Implementation

We will usually have only one implementation for such an interface, that is strange, isn't it wrong?

We are using interface to separate layers in this case.
We separate the business logic layer and infrastructure (database) layer.
And that is a valid interface usage.

## Moving Logic ...for Maniacs

Do you think the logic is moved well?
I have a proposal for maniacs.

An Item is probably a value object an moving the logic into value objects is even greater than moving the logic to entities.
That's because value objects are immutable and the logic is, therefore, super easily testable and super separated.

So let's move some logic into the Item
```php
class Item {
    /**
     * @var int
     */
    private $productId;

    /**
     * @var float
     */
    private $amount;

    // constructor

    public function calculatePrice(ProductPrices $productPrices): Money {
        $price = $productPrices->refreshPrice($this->productId);
        return $price->multiply($this->amount);
    }
}

class Cart {
    /**
     * var Item[]
     */
    private $items;

    // constructor

    public function calculateTotalPrice(ProductPrices $productPrices): Money {
        $prices = [];
        foreach ($this->items as $item) {
            $prices[] = $item->calculatePrice($productPrices);
        }

        return Money::sum($prices);
    }
}

// usage is still the same
$totalPrice = $cart->calculateTotalPrice($this->productPrices);
```

Even the Item has behavior now.
The Item doesn't expose the data and contains the logic that directly uses the item itself.

Maybe this paragraph isn't only for maniacs, but programmers are usually shocked just by the interface, so I decided to calm down a bit.

## Conclusion

Moving logic to entities is possible, use an interface that provides the necessary information and passes it to the method.
Logic in entities is easy to understand, leads to better testability, better layer separation, and better encapsulation.

## Contact

If you struggle with moving logic to entities, contact me, I'll help You. Business logic is my passion... [svatasimara.cz](http://svatasimara.cz/)
