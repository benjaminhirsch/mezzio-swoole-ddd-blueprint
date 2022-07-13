## Overview

### Domain
``app/src/App/Domain``  

This will contain all entities, enums, exceptions, interfaces, types and logic specific to the domain layer.

### Application
``app/src/App/Application``  

This layer contains all application logic. It is dependent on the domain layer, but has no dependencies on any other layer or project. This layer defines interfaces that are implemented by outside layers. For example, if the application need to access a notification service, a new interface would be added to application and an implementation would be created within infrastructure.

### Infrastructure
``app/src/App/Infrastructure``

This layer contains classes for accessing external resources such as file systems, web services, smtp, and so on. These classes should be based on interfaces defined within the application layer.