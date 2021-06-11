/**
 * Block: PMPro Membership Consult
 *
 * Displays the Membership Consult page and form.
 *
 */

 /**
  * Internal block libraries
  */
  const { __ } = wp.i18n;
  const {
     registerBlockType
 } = wp.blocks;
 
  /**
   * Register block
   */
  export default registerBlockType(
      'pmpro/consult-page',
      {
          title: __( 'Membership Consult Page', 'paid-memberships-pro' ),
          description: __( 'Displays the member\'s consult information and allows them to update the payment method.', 'paid-memberships-pro' ),
          category: 'pmpro',
          icon: {
             background: '#2997c8',
             foreground: '#ffffff',
             src: 'list-view',
         },
         keywords: [ __( 'pmpro', 'paid-memberships-pro' ) ],
         supports: {
         },
         attributes: {
         },
          edit() {
              return [
                  <div className="pmpro-block-element">
                    <span className="pmpro-block-title">{ __( 'Paid Memberships Pro', 'paid-memberships-pro' ) }</span>
                    <span className="pmpro-block-subtitle">{ __( 'Membership Consult Page', 'paid-memberships-pro' ) }</span>
                  </div>
             ];
          },
          save() {
            return null;
          },
        }
  );
 