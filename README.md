
# tequilrapido/node-bridge

```
 $output = (new PipeNodeRunner)
            ->setNodeExecutable('/path/to/node/executable')
            ->setPipe("something to pipe to the script")
            ->setScript("realpath/to/the/node/script")
            ->run();
```