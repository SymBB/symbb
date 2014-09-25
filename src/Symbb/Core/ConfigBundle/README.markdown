# SymbbCoreConfigBundle

This is the configuration bundle.
Currently ALL CORE Configurations are in this Bundle!
This is because currently we are forcing SF to use our Configuration and we dont allow to overwrite it
( Because we also define doctrine, framwork etc.. stuff what the system definitifly need )

Later we will rethink about it how we can handle the configuration correctly to make it posibil to overwrite stuff but be sure that all needed stuff is not overwrite able!
I think at the end we will move every configuration into its own Bundle only the "important" stuff will be prepended over this bundle ( but its not clear currently )

## Structure

### Api

In this Folder we have all Api Services.
Every Api Should extend from the SymbbCoreSystemBundle -> Api -> AbstractApi
Try to use a "Manager" in every Api.
Manager = working with Objects, throwing errors, returning Objects, fire events (usable in other code places also in none api stuff)
Api = working with array params and object, collection error messages ( only for API usage, will not be used in other code stuff )
Therefore no Api should make direct Database stuff use the Manager for it.

### Controller

In this Folder we have our Controller for this Bundle.

### DependencyInjection

In this Folder we should only have Stuff that will be injected or other Services then ApiServices or ManagerServies ( this 2 have own folder )

### Entity

Only Entity Classes.
Entity Classes have only set and getter! No logic!
Logic should be done in a Manager who is also fire events to manipulate the logic

### Event

We will putt all Event Classes into this folder.

### Form

SF2 Form Classes, should be not needed in our Api Based System. But if we will have some normale SF Controller/View stuff then this is the correct place for the FormType Classes.

### Manager

In this Folder we will put every "Manager".
Manager will handle Logic and Database Stuff for ONE Entity. Please avoid to create one Manager who is handling different Entites.
( Only if it is something like getting Forum and also child elements like topic, flags, post, etc.. but not e.g createForum and createPost in one Manager, use 2 and also try to inject one manager into a other if you need stuff from child objects )

### Menu

If you need a KNP Menu Class then create it in this Folder

### Resources

JS/CSS/Images etc... read SF Docu for more details ;)

### Twig

If you create some Twig Extensions. Put the classes into this folder