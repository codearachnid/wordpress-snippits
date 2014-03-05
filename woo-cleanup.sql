# cleanup all products and categories from WooCommerce
DELETE FROM {dbprefix}term_relationships WHERE object_id IN (SELECT ID FROM {dbprefix}posts WHERE post_type = 'product');
DELETE FROM {dbprefix}postmeta WHERE post_id IN (SELECT ID FROM {dbprefix}posts WHERE post_type = 'product');
DELETE FROM {dbprefix}posts WHERE post_type = 'product';

# To remove the taxonomies (which store the name of the attribute) and the terms 
# (which store the values) as well, you can use the following query
DELETE relations.*, taxes.*, terms.*
  FROM {dbprefix}term_relationships AS relations
  INNER JOIN {dbprefix}term_taxonomy AS taxes
    ON relations.term_taxonomy_id=taxes.term_taxonomy_id
  INNER JOIN {dbprefix}terms AS terms
    ON taxes.term_id=terms.term_id
  WHERE object_id IN (SELECT ID FROM {dbprefix}posts WHERE post_type='product');
