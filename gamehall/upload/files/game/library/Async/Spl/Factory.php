<?php

class Async_Spl_Factory {
    
    private static $subjects = array();
    private static $observers = null;
    
    public static function getSubject($name) {
        if (! isset(self::$subjects[$name])) {
            self::loadSubjects($name);
        }
        return self::$subjects[$name];
    }
    
    private static function loadSubjects($name) {
        if (is_null(self::$observers)) {
            self::loadObservers(); 
        }
        $subjectPath = __DIR__ . DIRECTORY_SEPARATOR . 'Subject';
        $juebjectList = Util_ClassLoader::loadClassesFromDir($subjectPath);
        foreach ($juebjectList as $subject) {
            if (! ($subject instanceof Async_Spl_Subject)) {
                continue;
            }
            if($subject->getName() != $name) {
                continue;
            }
            foreach (self::$observers as $observer) {
                if(in_array($subject->getName(), $observer->getSubjectList())) {
                    $subject->attach($observer);
                }
            }
            self::$subjects[$subject->getName()] = $subject;
        }
        if(! isset(self::$subjects[$name])) {
            self::$subjects[$name] = null;
        }
    }
    
    private static function loadObservers() {
        self::$observers = array();
        $observerPath = __DIR__ . DIRECTORY_SEPARATOR . 'Observer';
        $objectList = Util_ClassLoader::loadClassesFromDir($observerPath);
        foreach ($objectList as $object) {
            if (! ($object instanceof Async_Spl_Observer)) {
                continue;
            }
            $className = get_class($object);
            self::$observers[$className] = $object;
        }
    }
    
}
