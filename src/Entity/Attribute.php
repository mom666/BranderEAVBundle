<?php
namespace Brander\Bundle\EAVBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Werkint\Bundle\FrameworkExtraBundle\Model\Translatable;

/**
 * Аттрибут
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 *
 * @ORM\Entity()
 * @ORM\Table(name="brander_eav_attribute")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *   "input"    = "\Brander\Bundle\EAVBundle\Entity\AttributeInput",
 *   "select"   = "\Brander\Bundle\EAVBundle\Entity\AttributeSelect",
 *   "boolean"  = "\Brander\Bundle\EAVBundle\Entity\AttributeBoolean",
 *   "numeric"  = "\Brander\Bundle\EAVBundle\Entity\AttributeNumeric",
 *   "date"     = "\Brander\Bundle\EAVBundle\Entity\AttributeDate",
 *   "textarea" = "\Brander\Bundle\EAVBundle\Entity\AttributeTextarea",
 *   "location" = "\Brander\Bundle\EAVBundle\Entity\AttributeLocation"
 * })
 * @Serializer\Discriminator(field="discr", map={
 *   "input"    = "Brander\Bundle\EAVBundle\Entity\AttributeInput",
 *   "select"   = "Brander\Bundle\EAVBundle\Entity\AttributeSelect",
 *   "boolean"  = "Brander\Bundle\EAVBundle\Entity\AttributeBoolean",
 *   "numeric"  = "Brander\Bundle\EAVBundle\Entity\AttributeNumeric",
 *   "date"     = "Brander\Bundle\EAVBundle\Entity\AttributeDate",
 *   "textarea" = "Brander\Bundle\EAVBundle\Entity\AttributeTextarea",
 *   "location" = "Brander\Bundle\EAVBundle\Entity\AttributeLocation"
 * })
 * @Serializer\ExclusionPolicy("all")
 *
 * Переводные методы:
 * @method AttributeTranslation translate(string $lang)
 * @method AttributeTranslation[]|ArrayCollection getTranslations()
 * @method AttributeTranslation[] getATranslations()
 * @method AttributeTranslation mergeNewTranslations()
 * @method string getTitle()
 * @method string getHint()
 * @method string getPlaceholder()
 * @method string getPostfix()
 * *method AttributeTranslation setTitle(string $title)
 */
abstract class Attribute
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sets = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Type("string")
     * @Serializer\ReadOnly()
     * @Serializer\Expose()
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     * @Serializer\Groups("=read || g('admin')")
     * @Serializer\Expose()
     */
    protected $isRequired;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     * @Serializer\Groups("=read || g('admin')")
     * @Serializer\Expose()
     */
    protected $isFilterable;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     * @Serializer\Groups("=read || g('admin')")
     * @Serializer\Expose()
     */
    protected $isSortable;


    /**
     * RESERVED
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Groups("=read || g('admin')")
     * @Serializer\Expose()
     */
    protected $filterType;

    /**
     * RESERVED
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Groups("=read || g('admin')")
     * @Serializer\Expose()
     */
    protected $showType;

    /**
     * @ORM\OneToMany(targetEntity="Value", cascade={"all"}, mappedBy="attribute")
     * @var Value[]
     */
    protected $values;

    /**
     * @ORM\ManyToMany(targetEntity="Brander\Bundle\EAVBundle\Entity\AttributeSet", cascade={"persist"}, mappedBy="attributes")
     * @var AttributeSet[]|Collection
     */
    protected $sets;

    /**
     * @ORM\ManyToMany(targetEntity="\Brander\Bundle\EAVBundle\Entity\AttributeGroup", cascade={"persist"}, mappedBy="attributes")
     * @var AttributeGroup[]|Collection
     */
    protected $groups;

    // -- Value ---------------------------------------

    /**
     * @var string
     */
    protected $valueClass;

    /**
     * @param string $valueClass
     */
    public function setValueClass($valueClass)
    {
        $this->valueClass = $valueClass;
    }

    /**
     * @return Value
     */
    public function createValue()
    {
        $row = new $this->valueClass;
        /** @var Value $row */
        $row->setAttribute($this);
        return $row;
    }


    /**
     * @param Value $value
     * @return Value
     */
    public function assignValue(Value $value)
    {
        if (get_class($value) != $this->valueClass) {
            /** @var Value $tmp */
            $tmp = new $this->valueClass;
            $tmp->setId($value->getId())->setAttribute($this);
            return $tmp;
        }
        $value->setAttribute($this);
        return $value;
    }

    // -- Translations ------------------------------------

    use Translatable;

    /**
     * *virtual
     * @Serializer\Type("array<Brander\Bundle\EAVBundle\Entity\AttributeTranslation>")
     * @Serializer\Groups({"=read || g('admin')"})
     * @Serializer\Accessor(getter="getATranslations", setter="setATranslations")
     * @Serializer\Groups({"=g('translations') || g('admin')"})
     * @Serializer\Expose()
     * @Assert\Valid
     */
    protected $translations;

    /**
     * *virtual
     * @Serializer\Accessor(getter="getTitle", setter="setTitle")
     * @Serializer\Type("string")
     * @Serializer\Groups({"=read && !g('minimal')"})
     * @Serializer\Expose()
     */
    protected $title;

    /**
     * *virtual
     * @Serializer\Accessor(getter="getHint", setter="setHint")
     * @Serializer\Type("string")
     * @Serializer\Groups({"=read && !g('minimal')"})
     * @Serializer\Expose()
     */
    protected $hint;

    /**
     * *virtual
     * @Serializer\Accessor(getter="getPlaceholder", setter="setPlaceholder")
     * @Serializer\Type("string")
     * @Serializer\Groups({"=read && !g('minimal')"})
     * @Serializer\Expose()
     */
    protected $placeholder;

    /**
     * *virtual
     * @Serializer\Accessor(getter="getPostfix", setter="setPostfix")
     * @Serializer\Type("string")
     * @Serializer\Groups({"=read && !g('minimal')"})
     * @Serializer\Expose()
     */
    protected $postfix;

    // -- Accessors ---------------------------------------

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Value[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param Value[] $values
     *
     * @return $this
     */
    public function setValues(array $values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @return AttributeSet[]|Collection
     */
    public function getSets()
    {
        return $this->sets;
    }

    /**
     * @param AttributeSet[]|Collection $sets
     *
     * @return $this
     */
    public function setSets($sets)
    {
        $this->sets = $sets;
        return $this;
    }

    /**
     * @return AttributeGroup[]|Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param AttributeGroup[]|Collection $groups
     *
     * @return $this
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * @param boolean $isRequired
     * @return $this
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = (bool)$isRequired;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFilterable()
    {
        return $this->isFilterable;
    }

    /**
     * @param boolean $isFilterable
     * @return $this
     */
    public function setIsFilterable($isFilterable)
    {
        $this->isFilterable = (bool)$isFilterable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortable()
    {
        return $this->isSortable;
    }

    /**
     * @param boolean $isSortable
     *
     * @return $this
     */
    public function setIsSortable($isSortable)
    {
        $this->isSortable = (bool)$isSortable;
        return $this;
    }

}