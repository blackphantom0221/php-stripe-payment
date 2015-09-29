# Change Log

This project follows [Semantic Versioning](CONTRIBUTING.md).

## Proposals

We do not give estimated times for completion on `Accepted` Proposals.

- [Accepted](https://github.com/cartalyst/stripe/labels/Accepted)
- [Rejected](https://github.com/cartalyst/stripe/labels/Rejected)

---

### v1.0.5 - 2015-09-29

`ADDED`

- [Relay](https://stripe.com/relay) endpoints.

### v1.0.4 - 2015-09-18

`FIXED`

- Issue where the `amount` that was not being automatically converted properly.

`ADDED`

- A new `AmountConverter` class that automatically converts the `amount`.
- A methods to the Stripe class to get and set the Amount Converter class easily.

### v1.0.3 - 2015-07-08

`FIXED`

- Wrong returns on some docblocks.
- Incorrect coding standards.

### v1.0.2 - 2015-06-30

`ADDED`

- `.gitattributes` and `.travis.yml` file.

### v1.0.1 - 2015-06-03

`ADDED`

- Added new methods to create and update an account and to retrieve all the connected accounts.

### v1.0.0 - 2015-04-02

`INIT`

- Initial release.
