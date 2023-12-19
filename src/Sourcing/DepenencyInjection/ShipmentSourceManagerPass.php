<?php


namespace App\Sourcing\DepenencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class ShipmentSourceManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $tags = $container->findTags();
        $converter = new CamelCaseToSnakeCaseNameConverter();

        // loop over every declared tags,
        foreach ($tags as $tag) {
            // for the example I chose as convention to store every tagged services, whose tag starts with app.
            if (!str_starts_with($tag, 'app.')) {
                continue;
            }

            // The app.my.tag tag would expect the argument in the constructor to be named $appMyTagServices
            $name = $converter->denormalize(str_replace('.', '_', $tag)).'Services';

            // Prepare the services references
            $taggedServices = array_map(fn($serviceId) => new Reference($serviceId), array_keys($container->findTaggedServiceIds($tag)));

            // Creating a new service and injects the tagged services references
            // $container->register($name, InjectableTaggedServices::class)->setArguments([$taggedServices]);

            // // Signal symfony to inject the previously created service when encountering an argument with the TaggedServicesInterface and the computed name.
            // $container->registerAliasForArgument($name, TaggedServicesInterface::class);
        }
    }
}