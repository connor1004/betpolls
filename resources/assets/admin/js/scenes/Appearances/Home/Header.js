import React, { Component } from 'react';
import {
  Card, Stack, ResourceList, TextStyle
} from '@shopify/polaris';

class Header extends Component {
  render() {
    return (
      <Card>
        <ResourceList.Item>
          <Stack>
            <Stack.Item fill>
              <TextStyle variation="strong">League</TextStyle>
            </Stack.Item>
            <Stack.Item>
              <div style={{ width: '140px' }} />
            </Stack.Item>
          </Stack>
        </ResourceList.Item>
      </Card>
    );
  }
}

export default Header;
