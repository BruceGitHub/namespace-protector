# namespace-protector
A tool to validate namespace

Ispired by 
https://www.slideshare.net/MicheleOrselli/comunicare-condividere-e-mantenere-decisioni-architetturali-nei-team-di-sviluppo-approcci-e-strumenti
this https://docs.microsoft.com/en-us/dotnet/csharp/programming-guide/classes-and-structs/access-modifiers#:~:text=Class%20members%2C%20including%20nested%20classes,from%20outside%20the%20containing%20type. 
and for fun ...

This functionality allow to improve the information hiding at level of namespace Like C#/Java pubblic/private class. 
The idea is that namespace in some situations can be private at all except for a specific entry point. 
For example the namespace of third parts lib. Trought the json configuration it's possible define 

- public namespace
- private namespace 
- mode public by default in this setup only a private namespace it's validated or mode `private vendor` in which each access 
to namespace of vendor trigger a violation if not was added public namespace.

I think the in future the modes can be increase


For now it is a lab 
