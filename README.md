# Utils

## Translation

### Translation CRUD

Entity  должна выглядеть следующим образом: имплементирован  TranslatableEntityInterface и определен метод
getTranslatableFields. Возможно использовать PrivateDev\Utils\Entity\TranslationEntityTrait для добавления поля $translation.
После этого, при использовании PrivateDev\Utils\Controller\CRUD(L)Controller все переводы будут сохранятся в связанную сущность 
PrivateDev\Utils\Entity\Translation

```php
namespace PageBundle\Entity;


use PrivateDev\Utils\Entity\TranslatableEntityInterface;
use PrivateDev\Utils\Entity\Translation;
use Doctrine\ORM\Mapping as ORM;
use PrivateDev\Utils\Entity\EnableEntityTrait;
use PrivateDev\Utils\Entity\TimestampEntityTrait;
use PrivateDev\Utils\Entity\TranslationEntityTrait;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Property;

/**
 * @Definition()
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity(repositoryClass="PageBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Page implements TranslatableEntityInterface
{
    use TimestampEntityTrait;
    use EnableEntityTrait;
    use TranslationEntityTrait;

    public function getTranslatableFields()
    {
        return ['title'];
    }
    
    /**
     * 
     * @var string
     *
     * @Property()
     *
     */
    private $title;   // не должна быть @ORM\Column все переводы сохраняются для локали в Translation
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title, $language = null)
    {
        if ($language) {
            //метод в TranslationEntityTrait устанавливает перевод для поля вTranslation
            $this->setTranslationForField('title', $title, $language);    
        } else {
            $this->title = $title;
        }

        return $this;
    }

    /**
     * Get title
     *
     * @param null $language
     *
     * @return string
     */
    public function getTitle($language = null)
    {
        if ($language) {
            //метод в TranslationEntityTrait достает перевод для поля из Translation
            $title = $this->getTranslationForField('title', $language);
        } else {
            $title = $this->title;
        }

        return $title;
    }
    
}
```

### Translation transformer

Для трансформера необходимо имплементировать TranslatableTransformerInterface, тогда при использовании PrivateDev\Utils\JsonёTransformableJsonResponseBuilder
текущий язык будет установлен в трансформер. Можно использовать PrivateDev\Utils\Fractal\TranslatableTransformerTrait для определения $language и его акцессоров

```php
    <?php
    
    namespace PageBundle\Transformer;
    
    use PageBundle\Entity\Page;
    use PrivateDev\Utils\Fractal\TransformerAbstract;
    use PrivateDev\Utils\Fractal\TranslatableTransformerInterface;
    use PrivateDev\Utils\Fractal\TranslatableTransformerTrait;
    
    class PageTransformer extends TransformerAbstract implements TranslatableTransformerInterface
    {
        use TranslatableTransformerTrait;
    
        /**
         * @param Page $page
         *
         * @return array
         */
        public function transform($page) : array
        {
            return [
                'id'        => $page->getId(),
                // передаем текущий язык в нужный метод
                'title'     => $page->getTitle($this->getLanguage())
            ];
        }
    
        /**
         * @return string
         */
        public function getResourceKey() : string
        {
            return 'page';
        }
    }
```
