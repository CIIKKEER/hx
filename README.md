
#**SUMMARY**

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

/* Access version information directly as a property
 *
 */
gf()->fun->debug->print_r(gf()->version->about())->die
```

This single line demonstrates the core paradigm: you don't create or configure objects—you simply access them, and the tree assembles itself on demand.

---

What It Provides

hx organizes all services as a lazy-loaded property tree. The root gf() provides access to:

```
gf()                     // Root instance
├── fun                  // Functional utilities (stdclass, helpers)
│   ├── json		 
│   ├── strings          
│   ├── array            
│   ├── stdclass         
│   ├── ...         
│   ├── ...         
│   ├── ...         
│   ├── ...         
│   ├── ...         
│   ├── ...         
│   ├── ...         
│   ├── ...         
│   └── regx             
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
├── ...			 	
├── ...			 	
├── ...			 	
├── ...			 	
├── ...			 	
├── ...			 	
├── ...			 	
├── ...			 	
├── ...			 	
├── ...			 	
└── exception            // Exception handler (auto-registers on first access)
```

Every node is only instantiated when you first access it. Nothing is loaded until you need it.

---

Basic Usage Examples

Database Query

```php
/* testing ...
 *
 */
gf()->fun->debug->print_r (gf()->db->mysqli->open_with_env_json(__DIR__ . '/../../../env/env.json')->get_db_information())->die('ok');

/* test db no transcation
 *
 */
$db = gf()->db->mysqli->open_with_env_json(__DIR__ . '/../../../env/env.json');$db->connect()->query("select version(),now();")->go()->for_each ( function($k,$v)
{
	gf()->fun->debug->print_r ($k,$v);
});
		
/* The entire database stack initializes only when you access ->db
 *
 */ 
$result = $db->query('SELECT * FROM users WHERE id = ?')->ai(1)/* bind integer parameter */->go()/* execute */->for_each ( function($k, $v)
{
    echo $row->name;
});
```
Routing

```php
gf()->route->add('/test/about' ,function (i_request $r,i_response $s) { return $s->success('test.ok');});
```

Runtime Service Replacement (Mock/Test)

```php
/* Replace the database service with a mock at runtime
 *
 */
gf()->ado_inject('db', new MyMockDatabase());

/* Simulate Redis injection using the new Redis library
 *
 */
 gf()->cache->ado_inject('redis', c_redis_mock_test_inject::class);

/* All subsequent code using gf()->db now uses your mock no container rebuild, no configuration changes needed
 *
 */
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

