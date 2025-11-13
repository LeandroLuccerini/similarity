# Similarity

A lightweight PHP library for measuring the similarity between strings and dates with flexible normalization, transliteration, and fuzzy comparison strategies.

## Overview

The **Similarity** package provides utilities to compare textual or date-based inputs and determine how closely they match.  
It’s designed to be **extensible**, **locale-aware**, and **safe for fuzzy matching** — making it ideal for use cases such as data deduplication, data cleaning, or record linkage.

The core idea is that different types of data (like names, text, or dates) require different similarity strategies, so the library offers multiple specialized classes.

---

## Main Components

### `StringFuzzySimilarity`

Compares two strings with a fuzzy matching algorithm.  
Useful when dealing with typos, transliteration differences, or minor formatting variations.

- **Normalization**: Removes punctuation, trims whitespace, and can optionally transliterate text to Latin.
- **Algorithm**: Combines `similar_text` and normalized Levenshtein distance.
- **Use case**: Matching names like `José` vs `Jose`, or `McDonald’s` vs `McDonalds`.

```php
$similarity = new StringFuzzySimilarity(
                new StringNormalizer(
                  new TransliteratorFactory()
                )
              );
$result = $similarity->similarity('José García', 'Jose Garcia'); // e.g., 0.97
```

---

### `DateFuzzySimilarity`

Compares two date values even if they use different formats or delimiters.

- **Normalization**: Converts supported formats (e.g., `YYYY-MM-DD`, `DD/MM/YYYY`, `MM.DD.YYYY`) into a canonical form.
- **Algorithm**: Computes a similarity score based on date component proximity (e.g., days, months, years).
- **Use case**: Matching `12-03-1990` and `1990/03/12` as the same date.

```php
$similarity = new DateFuzzySimilarity(
                new DateFuzzySimilarityConfiguration(
                  new DatePartsWeights(),
                  new DateDiffPenalty()
                ),
                new DateNormalizer()
              );
$result = $similarity->similarity('1990-03-12', '12/03/1990'); // 1.0
```

---

### `StringExactSimilarity`

Compares two strings for an **exact match** after normalization.

- **Normalization**: Cleans strings but does not introduce fuzziness.
- **Algorithm**: Returns `1.0` if normalized strings are identical, otherwise `0.0`.
- **Use case**: Validating IDs, codes, or fields that must match exactly.

```php
$similarity = new StringExactSimilarity();
$result = $similarity->similarity('ABC123', 'abc123'); // 1.0
```

---

## Factory Usage Example

The library provides a simple factory for creating the right similarity strategy depending on the data type or context.

```php
use Szopen\Similarity\SimilarityFactory;

$factory = new SimilarityFactory(
            new DateFuzzySimilarityConfiguration(
              new DatePartsWeights(),
              new DateDiffPenalty(),
            )
        );

// Automatically selects a suitable comparator
$stringSim = $factory->create(SimilarityFactory::STRING_FUZZY);
$dateSim = $factory->create(SimilarityFactory::DATE_FUZZY);

// Compute similarity
echo $stringSim->similarity('Leandro', 'Leandor'); // e.g., 0.9
echo $dateSim->similarity('2023-11-12', '12.11.2023'); // 1.0
```

You can extend or customize the factory to add your own similarity strategies.

---

## Installation

```bash
composer require szopen/similarity
```

---

## Requirements

- PHP 8.2+
- `ext-intl` (recommended for proper transliteration)
- `ext-iconv` (recommended as fallback from `ext-intl`)

---

## License

This project is licensed under the [MIT LICENSE](https://opensource.org/license/mit).

---

## Contributing

Contributions are welcome!  
Please open an issue or submit a pull request if you’d like to add new normalization strategies or similarity metrics.
