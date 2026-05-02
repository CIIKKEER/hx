

#**SUMMARY**

PHP helper library fully embracing PHP's dynamic features, using 'property access' as a unified service entry point to build a lightweight, high-performance tree structure, allowing runtime replacement and reorganization—enabling developers to express business intent in a free-flowing manner without framework restrictions.

##I. Core Innovation: Reconstructing the Service Invocation Paradigm with "Programmable Dynamic Properties"
>Traditional PHP frameworks rely on dependency injection containers as the central mechanism for service assembly, with configurations typically solidified during the application bootstrap phase. This architecture, however, deeply integrates service resolution with property access through the ingenious ado and ado_inject methods within c_base_class. ado_inject is particularly revolutionary: when a property already exists, it does not merely overwrite it. Instead, it creates an entirely new, isolated instance for resolution via $this->new()->ado($k, $v, $k)->$k. This essentially constructs an "atomic resolution sandbox," ensuring that the process of generating a new value remains uncontaminated by the current object's state, while fully reusing the resolution logic. This grants the architecture the ability to dynamically replace service implementations at any point during runtime—whether for testing mocks, feature toggles, or hot upgrades—all achievable with a single line of code, eliminating the need for container rebuilds or complex service provider overrides. It represents a creative exploitation of PHP's magic method capabilities, elevating property access from passive storage to a unified entry point for active computation and dynamic assembly.

##II. Evolution of the Service Locator: Perfect Symbiosis of a Global Singleton and IDE Contracts

>The gf() global function is often criticized as a source of hidden dependencies and a testing obstacle. This architecture neutralizes that criticism through two specific design choices. First, the core hx class utilizes @property annotations to provide comprehensive IDE type declarations for all sub-modules. This allows developers using gf()->route to enjoy code completion and static analysis experiences identical to those of explicit instantiation. Second, ado_inject provides a reverse control channel: test code does not need to intrude upon the container's internals; it can simply execute ado_inject directly on the global instance to replace a service. In essence, this transforms the service locator into a "programmable global registry" with a clean modification interface and clearly defined scope, all while remaining fully compatible with modern IDE toolchains. The author does not disregard testing but instead offers a testing instrumentation approach better aligned with the intuitive nature of dynamic languages.

##III. An Alternative Implementation of Dependency Inversion: Runtime dynamic property object tree and  Module Autonomy

>Mainstream architecture advocates for a top-down dependency flow, achieving module decoupling through interfaces and constructor injection. This architecture, conversely, establishes a tree-shaped dependency topology: all modules depend solely on the central gf() locator, with no direct references between modules. The elegance of this design lies in the fact that the impact radius of a module replacement is strictly confined to the mapping table within the locator (specifically, the ado chain within hx::__get). For instance, replacing c_mysqli with c_pdo requires modifying only a single line mapping within c_db::__get; all business code retrieving connections via gf()->db remains entirely unchanged. More radically, combined with ado_inject, one can even replace a specific connection at runtime with a coroutine version or a read-only replica version, without needing a pre-designed, complex resolver interface. This inverted dependency tree substitutes "dynamic dispatch" for "static abstraction," trading a degree of compile-time type safety for extreme runtime adaptability.

##IV. Normalized Runtime: A Stylized Encapsulation of PHP's Native Capabilities

>The framework's encapsulation of extensions like MySQLi and Redis is by no means a simple proxy. Taking the database layer as an example, c_bind_parameter utilizes regex and placeholder protection mechanisms to abstract the cumbersome details of native prepared statement parameter binding into a fluent, chainable interface: ai()->as()->go(). Internally, it manages tedious specifics such as parameter count validation, type string concatenation, and bulk array expansion, ultimately delivering a unified and intuitive calling experience to the developer. This normalization pursues "operating different resources with the same mindset": whether interacting with a database or cache, the developer consistently encounters the pattern of gf()->module->action()->chain()->execute(). It rejects the addition of unnecessary abstraction layers simply to conform to PSR standards, opting instead to directly refine the invocation forms of native PHP functions to better align with human cognitive flow.

##V. The Implicit Permeation of Performance Consciousness

>Numerous details within the architecture betray a keen awareness of performance considerations. The __get methods within hx and c_fun implement lazy loading via ado, instantiating objects only upon first access and thereby avoiding the overhead of full initialization during bootstrap. c_route stores its routing table in static variables; while the benefit may be marginal in traditional PHP-FPM environments, it prevents the repeated construction of the route tree per request in long-running memory-resident contexts (e.g., Swoole). While c_query caches the entire result set at once, its for_each interface preserves the possibility of row-by-row processing without forcing array conversion. Even more noteworthy is the systematic use of weak references: c_trans, c_bind_parameter, and c_query all hold connection objects via WeakReference. This simultaneously prevents circular reference memory leaks and ensures that the connection lifecycle remains decoupled from the query object. These are not mere technical flourishes but rather evidence of a profound understanding and proactive mastery of PHP's memory management mechanisms.

##Conclusion

>This codebase constitutes a lucid and forceful critique of mainstream PHP engineering culture. Without blindly adhering to PSR specifications or piling on design patterns, it demonstrates that a deep insight into the dynamic nature of the language itself is sufficient to construct an application skeleton that is clearly structured, highly extensible, and performant. Its designs—"Properties as Services," "Dynamic Injection Sandbox," and "Tree Dependency Topology"—offer uniquely ingenious solutions for scenarios demanding extreme development velocity and runtime flexibility. It serves as a reminder to every PHP developer: a framework is ultimately a tool, and the highest state of a tool is to disappear from the stream of thought—allowing the developer to focus solely on business expression, undistracted by the framework itself. In this dimension, this architecture achieves a level of simplicity and transparency that many heavyweight frameworks fail to reach.


A zero-dependency, high-performance PHP helper for dynamic property trees.

PHP helper library fully embracing PHP's dynamic features, using 'property access' as a unified service entry point to build a lightweight, high-performance tree structure, allowing runtime replacement and reorganization—enabling developers to express business intent in a free-flowing manner without framework restrictions.

---

Installation

Install via Composer:

```bash
composer require ciikkeer/hx
```

Requirements: PHP 8.0+

---

Quick Start

Once installed, the global gf() function is your universal entry point. Here's the simplest possible example to verify everything is working:

```php
<?php
require 'vendor/autoload.php';

// Access version information directly as a property
echo gf()->version->about()->version; // Outputs: 1.0.68
```

This single line demonstrates the core paradigm: you don't create or configure objects—you simply access them, and the tree assembles itself on demand.

---

What It Provides

hx organizes all services as a lazy-loaded property tree. The root gf() provides access to:

```
gf()                      // Root instance
├── fun                  // Functional utilities (stdclass, helpers)
├── db                   // Database factory
│   ├── mysqli           // MySQLi driver
│   └── pdo              // PDO driver
├── cache                // Cache abstraction
├── config               // Configuration manager
├── version              // Library metadata
├── test                 // Testing utilities
├── cli                  // CLI helpers
├── os                   // OS-level utilities
├── route                // Routing engine
├── reflection           // Reflection tools
├── pay                  // Payment integration (WeChat, Alipay)
└── exception            // Exception handler (auto-registers on first access)
```

Every node is only instantiated when you first access it. Nothing is loaded until you need it.

---

Basic Usage Examples

Database Query

```php
// The entire database stack initializes only when you access ->db
$result = gf()->db->mysqli
    ->connect('default')
    ->query('SELECT * FROM users WHERE id = ?')
    ->ai(1)          // Bind integer parameter
    ->go();          // Execute

// Iterate results
$result->for_each(function(string $key, $row) {
    echo $row->name;
});
```

Routing

```php
gf()->route
    ->get('/hello/{name}', function($name) {
        return "Hello, $name!";
    })
    ->post('/submit', function() {
        // handle POST
    });
```

Runtime Service Replacement (Mock/Test)

```php
// Replace the database service with a mock at runtime
gf()->ado_inject('db', new MyMockDatabase());

// All subsequent code using gf()->db now uses your mock
// No container rebuild, no configuration changes needed
```

Accessing the Data Container

Every object in the tree provides a dc() method for isolated temporary data:

```php
gf()->dc()->myTempValue = 'stored safely';
```

---

Design Philosophy

This section explains the "why" behind the architecture. For usage, the examples above are all you need.

I. Properties as Programmable Service Entries

Traditional frameworks use dependency injection containers as a central assembly mechanism. hx fundamentally rethinks this: property access triggers intelligent, lazy service resolution.

The core lies in c_base_class::ado(), which unifies three value types—class names, callables, and objects—under a single resolution pipeline. Its counterpart, ado_inject(), is the revolutionary piece: when replacing an existing property, it creates a clean, isolated instance via $this->new()->ado(...) before assigning the result. This "atomic resolution sandbox" ensures the new value is built without contamination from the current object's state, enabling pure one-line runtime replacements.

II. The Global Singleton, Rehabilitated

The gf() global function is often criticized for hidden dependencies and testability issues. hx neutralizes both concerns:

· IDE Compatibility: The root hx class uses comprehensive @property annotations. Typing gf()->route gives full autocompletion, identical to explicit instantiation.
· Testability: ado_inject provides a clean, external modification channel. Test code simply calls gf()->ado_inject('db', $mock) without intruding into any container internals.

The result is a "programmable global registry" with a clean interface and fully defined scope.

III. Tree-Shaped Dependency Topology

Instead of a top-down dependency flow via constructor injection, hx establishes a tree topology: all modules depend solely on gf(), with no direct inter-module references.

The impact of replacing a module is strictly confined. Swapping c_mysqli for c_pdo requires changing only one line in c_db::__get. All business code using gf()->db remains untouched. Combined with ado_inject, you can even switch to a coroutine or read-replica connection at runtime—without a pre-designed resolver interface.

This trades a degree of compile-time type safety for extreme runtime adaptability.

IV. Normalized Runtime Experience

Encapsulation of native extensions (MySQLi, Redis) pursues a single goal: operating different resources with the same mindset.

The database layer's c_bind_parameter abstracts tedious prepared-statement details into a fluent, chainable interface (ai()->as()->go()). Whether interacting with a database, cache, or payment gateway, developers consistently encounter the pattern:

```
gf()->module->action()->chain()->execute()
```

No unnecessary abstraction layers are added just to conform to standards—native PHP invocation forms are refined to align with human cognitive flow.

V. Performance by Design

Performance is not an afterthought but woven into the architecture:

· Lazy Loading: __get triggers instantiation only on first access. No bootstrap overhead.
· Static Routing Table: c_route stores its routing tree in static variables, preventing repeated construction in long-running contexts like Swoole.
· Systematic Weak References: c_trans, c_bind_parameter, and c_query all hold connection objects via WeakReference, preventing circular reference memory leaks and decoupling connection lifecycles from query objects.

These details reveal a deep understanding of PHP's memory management—not technical flourishes, but deliberate mastery.

---

Conclusion

This codebase is a lucid critique of mainstream PHP engineering culture. Without blindly adhering to PSR specifications or piling on design patterns, it demonstrates that deep insight into the language's dynamic nature is sufficient to construct an application skeleton that is clearly structured, highly extensible, and performant.

Its designs—"Properties as Services," "Dynamic Injection Sandbox," "Tree Dependency Topology"—offer uniquely ingenious solutions for scenarios demanding extreme development velocity and runtime flexibility.

A framework is ultimately a tool, and the highest state of a tool is to disappear from the stream of thought—allowing the developer to focus solely on business expression. In this dimension, hx achieves a level of simplicity and transparency that many heavyweight frameworks fail to reach.

---

Contributing

Issues and pull requests are welcome. Please ensure any changes align with the core philosophy of zero-dependency and native PHP capability utilization.

---

License

Apache License 2.0. See LICENSE for full details.

